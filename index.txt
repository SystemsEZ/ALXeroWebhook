<?php
$webhook_secret = getenv("+yZzHTa/7Cz4Pvyr+VCDQdPtW3nuZIKdFZ8mHy5232kjOQ8E4kil8ViAMRqO2FQeN/fKGHKmiCvfoSYDs1JP0Q==");
$zohoWebhookUrl = getenv("https://flow.zoho.com/885711007/flow/webhook/incoming?zapikey=1001.f9fb9a68b0e970672209cca8bc6d5774.7c55897236eb0383e19f28cecc014bba&isdebug=false");

$raw_body = file_get_contents("php://input");
$received_signature = $_SERVER['HTTP_XXEROSIGNATURE'] ?? '';
$expected_signature = base64_encode(hash_hmac('sha256', $raw_body, $webhook_secret, true));

if (hash_equals($expected_signature, $received_signature)) {
    // Forward to Zoho Flow
    $ch = curl_init($zohoWebhookUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $raw_body);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_exec($ch);
    curl_close($ch);
    http_response_code(200);
    echo "✅ Validated & forwarded";
} else {
    http_response_code(401);
    echo "❌ Invalid signature";
}