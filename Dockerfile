FROM php:8.2-apache
 
RUN apt-get update && apt-get install -y \
    unzip git curl libpng-dev libonig-dev libxml2-dev zip \
&& docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd
 
# Enable rewrite
RUN a2enmod rewrite
 
WORKDIR /var/www/html
 
COPY . .
 
RUN unzip vendor.zip -d /var/www/html/ && rm vendor.zip
 
# Set document root
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
 
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf
 
# ✅ FIX: Proper Apache config (wrapped correctly)
RUN echo '<Directory /var/www/html/public>
    AllowOverride All
    Require all granted
</Directory>' > /etc/apache2/conf-available/laravel.conf \
&& a2enconf laravel
 
# Permissions
RUN chown -R www-data:www-data /var/www/html \
&& chmod -R 775 storage bootstrap/cache
 
EXPOSE 80
 
CMD ["apache2-foreground"]
