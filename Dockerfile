FROM node:22-bookworm-slim AS node

FROM php:8.4-fpm

# Cài đặt các thư viện hệ thống cần thiết
RUN apt-get update && apt-get install -y \
    unzip zip git curl \
    libzip-dev libpng-dev libonig-dev libxml2-dev \
    libjpeg-dev libfreetype6-dev libicu-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Cài đặt PHP extensions cho Laravel 13
# 'fileinfo' thường có sẵn, nhưng 'intl' và 'gd' là bắt buộc cho nhiều tính năng mới
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
    pdo_mysql \
    mbstring \
    zip \
    bcmath \
    xml \
    intl \
    gd

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY --from=node /usr/local/bin/node /usr/local/bin/node
COPY --from=node /usr/local/bin/corepack /usr/local/bin/corepack
COPY --from=node /usr/local/lib/node_modules /usr/local/lib/node_modules
RUN ln -sf /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm \
    && ln -sf /usr/local/lib/node_modules/npm/bin/npx-cli.js /usr/local/bin/npx

WORKDIR /var/www
