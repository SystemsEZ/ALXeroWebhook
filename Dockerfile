FROM php:8.1-apache

# Enable .htaccess and mod_rewrite (not needed here, but safe)
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy everything into the Apache web root
COPY . /var/www/html

# Expose port 80 (default for Apache)
EXPOSE 80
