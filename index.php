<?php
$webhook_secret = getenv("XERO_SECRET");
$zohoWebhookUrl = getenv("ZAPI_WEBHOOK_URL");

$raw_body = file_get_contents("php://input");
$headers = getallheaders();
$received_signature = $headers['x-xero-signature'] ?? '';
$expected_signature = base64_encode(hash_hmac('sha256', $raw_body, $webhook_secret, true));

file_put_contents("php://stderr", "Expected: " . $expected_signature . "\n", FILE_APPEND);
file_put_contents("php://stderr", "Received: " . $received_signature . "\n", FILE_APPEND);

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
