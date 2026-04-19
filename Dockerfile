FROM node:22-bookworm-slim AS node

FROM php:8.4-fpm

# Cài đặt các thư viện hệ thống
RUN apt-get update && apt-get install -y \
    unzip zip git curl \
    libzip-dev libpng-dev libonig-dev libxml2-dev \
    libjpeg-dev libfreetype6-dev libicu-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Cài đặt PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring zip bcmath xml intl gd

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY --from=node /usr/local/bin/node /usr/local/bin/node
COPY --from=node /usr/local/bin/corepack /usr/local/bin/corepack
COPY --from=node /usr/local/lib/node_modules /usr/local/lib/node_modules
RUN ln -sf /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm \
    && ln -sf /usr/local/lib/node_modules/npm/bin/npx-cli.js /usr/local/bin/npx


# THIẾT LẬP THƯ MỤC LÀM VIỆCh
WORKDIR /var/www/html

# DÒNG QUAN TRỌNG 1: Mang code từ GitHub vào trong Docker
COPY . .

# DÒNG QUAN TRỌNG 2: Cài đặt các thư viện Laravel bên trong Docker
RUN composer install --no-dev --optimize-autoloader

# CẤP QUYỀN (Để không bị lỗi trang trắng)
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# LỆNH CHẠY
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]



COPY docker/app-entrypoint.sh /usr/local/bin/app-entrypoint.sh
RUN chmod +x /usr/local/bin/app-entrypoint.sh

ENTRYPOINT ["app-entrypoint.sh"]
CMD ["php-fpm"]

