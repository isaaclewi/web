FROM php:8.2-apache

# Installer extensions nécessaires
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev zip unzip git curl \
    && docker-php-ext-install pdo pdo_mysql

# Activer mod rewrite (important pour Laravel)
RUN a2enmod rewrite

# Copier le projet
COPY . /var/www/html

# Donner les droits
RUN chown -R www-data:www-data /var/www/html

WORKDIR /var/www/html

# Installer composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN composer install

EXPOSE 80
