FROM php:8.2-apache
 
# Install system dependencies
RUN apt-get update && apt-get install -y \
    unzip git curl libpng-dev libonig-dev libxml2-dev zip \
&& docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd
 
# Enable Apache rewrite
RUN a2enmod rewrite
 
# Set working directory
WORKDIR /var/www/html
 
# Copy project files
COPY . .
 
# Extract vendor (FASTER than composer install)
RUN unzip vendor.zip -d /var/www/html/ && rm vendor.zip
 
# Set Apache document root to public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
 
# Set permissions (CRITICAL for Laravel)
RUN chown -R www-data:www-data /var/www/html \
&& chmod -R 775 storage bootstrap/cache
 
# Expose port
EXPOSE 80
 
CMD ["apache2-foreground"]
