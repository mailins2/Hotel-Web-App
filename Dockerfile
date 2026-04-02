FROM php:8.4-fpm


RUN apt-get update && apt-get install -y \
    unzip zip git curl \
    libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip

>>>>>>> f78b4d2 (setup laravel docker mysql)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www