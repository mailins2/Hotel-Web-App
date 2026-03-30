FROM php:8.4-fpm

# Cài các package cần thiết
RUN apt-get update && apt-get install -y \
    gnupg \
    curl \
    apt-transport-https \
    unixodbc-dev \
    git \
    zip \
    unzip \
    libzip-dev

# Add Microsoft repo
RUN curl https://packages.microsoft.com/keys/microsoft.asc | gpg --dearmor -o /usr/share/keyrings/microsoft.gpg \
    && echo "deb [signed-by=/usr/share/keyrings/microsoft.gpg] https://packages.microsoft.com/debian/12/prod bookworm main" > /etc/apt/sources.list.d/mssql-release.list

# Cài SQL Server driver
RUN apt-get update && ACCEPT_EULA=Y apt-get install -y msodbcsql18

# Cài PHP extensions cần thiết
RUN docker-php-ext-install pdo zip

# Cài SQL Server extensions cho PHP
RUN pecl install sqlsrv pdo_sqlsrv \
    && docker-php-ext-enable sqlsrv pdo_sqlsrv

# ✅ Cài Composer (QUAN TRỌNG)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www