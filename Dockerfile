# Stage 1: Build Frontend
FROM node:22-bookworm-slim AS node-builder
WORKDIR /app
COPY package*.json ./
RUN if [ -f package-lock.json ]; then npm ci --include=optional; else npm install --include=optional; fi
COPY . .
RUN npm run build

# Stage 2: PHP Production
FROM php:8.4-fpm-bookworm

RUN apt-get update && apt-get install -y \
    unzip zip git curl libzip-dev libpng-dev libonig-dev \
    libxml2-dev libjpeg-dev libfreetype6-dev libicu-dev \
    cron \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# --- THÊM NODE.JS VÀO ĐÂY ---
COPY --from=node-builder /usr/local/bin/node /usr/local/bin/node
COPY --from=node-builder /usr/local/lib/node_modules /usr/local/lib/node_modules
RUN ln -sf ../lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm \
    && ln -sf ../lib/node_modules/npm/bin/npx-cli.js /usr/local/bin/npx \
    && node -v \
    && npm -v
# ---------------------------

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring zip bcmath xml intl gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy toàn bộ code trước
COPY . .

# Copy thư mục build từ stage trước
COPY --from=node-builder /app/public/build /var/www/public/build
COPY --from=node-builder /app/node_modules /var/www/node_modules

RUN composer install --no-dev --optimize-autoloader
RUN chown -R www-data:www-data storage bootstrap/cache

# 🔥 THIẾT LẬP CRON JOB - GỌI TRỰC TIẾP COMMAND
RUN echo "* * * * * root cd /var/www && /usr/local/bin/php artisan bookings:cancel-expired >> /var/www/storage/logs/cron.log 2>&1" > /etc/cron.d/laravel \
    && chmod 0644 /etc/cron.d/laravel \
    && crontab /etc/cron.d/laravel \
    && touch /var/www/storage/logs/cron.log \
    && chmod 777 /var/www/storage/logs/cron.log

COPY docker/app-entrypoint.sh /usr/local/bin/app-entrypoint.sh
RUN chmod +x /usr/local/bin/app-entrypoint.sh

ENTRYPOINT ["app-entrypoint.sh"]
CMD ["php-fpm"]
