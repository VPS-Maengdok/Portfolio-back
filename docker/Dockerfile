FROM php:8.4-fpm-bookworm

RUN apt-get update && apt-get install -y \
    git unzip zip curl libpq-dev libonig-dev libxml2-dev wkhtmltopdf fonts-noto-cjk \
    && docker-php-ext-install pdo pdo_pgsql

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
