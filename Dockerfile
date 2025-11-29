FROM php:8.1-apache

# Install PostgreSQL extension and required libraries
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache mod_rewrite for clean URLs
RUN a2enmod rewrite

# Copy application files to Apache directory
COPY . /var/www/html/

# Create logs directory
RUN mkdir -p /var/www/html/logs

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 777 /var/www/html/logs

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]