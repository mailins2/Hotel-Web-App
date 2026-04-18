# Stage 1: Node để build frontend
FROM node:22-bookworm-slim AS node

WORKDIR /app
COPY . .
RUN npm install && npm run build

# Stage 2: PHP
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

# THIẾT LẬP THƯ MỤC LÀM VIỆC
WORKDIR /var/www/html

# Copy code
COPY . .

# Copy build frontend từ stage node
COPY --from=node /app/public/build /var/www/html/public/build

# Cài đặt Laravel
RUN composer install --no-dev --optimize-autoloader

# Cấp quyền
RUN chown -R www-data:www-data storage bootstrap/cache

# Entrypoint
COPY docker/app-entrypoint.sh /usr/local/bin/app-entrypoint.sh
RUN chmod +x /usr/local/bin/app-entrypoint.sh

ENTRYPOINT ["app-entrypoint.sh"]

# Chạy PHP-FPM (KHÔNG dùng artisan serve)
CMD ["php-fpm"]