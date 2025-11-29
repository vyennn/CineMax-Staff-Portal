# PHP + Apache
FROM php:8.2-apache

# Install MySQLi extension
RUN docker-php-ext-install mysqli

# Copy project files
COPY . /var/www/html/

# Enable mod_rewrite
RUN a2enmod rewrite

# Expose port 80
EXPOSE 80
