FROM php:8.4-fpm

# Cài đặt các thư viện hệ thống cần thiết
RUN apt-get update && apt-get install -y \
    unzip zip git curl \
    libzip-dev libpng-dev libonig-dev libxml2-dev \
    libjpeg-dev libfreetype6-dev libicu-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Cài đặt PHP extensions
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

# THAY ĐỔI Ở ĐÂY:
WORKDIR /var/www/html

# THIẾU CÁI NÀY: Copy toàn bộ code vào WORKDIR
COPY . .

# Chạy composer install để đảm bảo vendor được cài đặt đúng trong môi trường docker
RUN composer install --no-dev --optimize-autoloader

# THÊM CÁI NÀY: Cấp quyền cho Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Lệnh chạy mặc định (có thể bị ghi đè bởi Start Command trên Railway)
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]