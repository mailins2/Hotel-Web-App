# Stage 1: Build Frontend
FROM node:22-bookworm-slim AS node-builder
WORKDIR /app
COPY . .
RUN if [ -f package.json ]; then npm install && npm run build; fi

# Stage 2: PHP Production
FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    unzip zip git curl libzip-dev libpng-dev libonig-dev \
    libxml2-dev libjpeg-dev libfreetype6-dev libicu-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring zip bcmath xml intl gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy toàn bộ code trước
COPY . .

# CHỈ copy thư mục build từ stage trước (để không đè mất index.php trong public)
COPY --from=node-builder /app/public/build /var/www/public/build

RUN composer install --no-dev --optimize-autoloader
RUN chown -R www-data:www-data storage bootstrap/cache

COPY docker/app-entrypoint.sh /usr/local/bin/app-entrypoint.sh
RUN chmod +x /usr/local/bin/app-entrypoint.sh

ENTRYPOINT ["app-entrypoint.sh"]
CMD ["php-fpm"]