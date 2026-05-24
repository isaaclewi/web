# ==============================
# 1. IMAGE BASE
# ==============================
FROM php:8.2-apache

# ==============================
# 2. INSTALL SYSTEM DEPENDENCIES
# ==============================
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    unzip \
    zip \
    libonig-dev \
    && docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath gd

# ==============================
# 3. ENABLE APACHE MOD REWRITE
# ==============================
RUN a2enmod rewrite

# ==============================
# 4. SET WORKDIR
# ==============================
WORKDIR /var/www/html

# ==============================
# 5. COPY PROJECT FILES
# ==============================
COPY . .

# ==============================
# 6. INSTALL COMPOSER
# ==============================
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ==============================
# 7. INSTALL PHP DEPENDENCIES
# ==============================
RUN composer install --no-dev --optimize-autoloader

# ==============================
# 8. FIX LARAVEL STORAGE & CACHE PATHS (IMPORTANT)
# ==============================
RUN mkdir -p \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    bootstrap/cache

# ==============================
# 9. PERMISSIONS (CRITICAL ON RENDER)
# ==============================
RUN chmod -R 775 storage bootstrap/cache

# ==============================
# 10. LARAVEL OPTIMIZATION
# ==============================
RUN php artisan config:clear || true
RUN php artisan cache:clear || true
RUN php artisan view:clear || true
RUN php artisan config:cache || true

# ==============================
# 11. APACHE CONFIG (POINT TO PUBLIC FOLDER)
# ==============================
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# ==============================
# 12. EXPOSE PORT (RENDER USES 10000)
# ==============================
EXPOSE 10000

# ==============================
# 13. START APACHE
# ==============================
CMD ["apache2-foreground"]
