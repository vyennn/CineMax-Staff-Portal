# PHP + Apache official image
FROM php:8.2-apache

# Copy all project files to Apache root
COPY . /var/www/html/
git add .
# Enable mod_rewrite if needed
RUN a2enmod rewrite

# Expose default HTTP port
EXPOSE 80
