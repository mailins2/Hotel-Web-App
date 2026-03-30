FROM php:8.4-fpm

# Cài tool cần thiết
RUN apt-get update && apt-get install -y \
    gnupg2 curl apt-transport-https ca-certificates \
    unzip zip git \
    unixodbc-dev \
    build-essential \
    libgssapi-krb5-2 \
    && rm -rf /var/lib/apt/lists/*

# Thêm Microsoft repo
RUN curl -fsSL https://packages.microsoft.com/keys/microsoft.asc | gpg --dearmor -o /usr/share/keyrings/microsoft.gpg

RUN echo "deb [signed-by=/usr/share/keyrings/microsoft.gpg] https://packages.microsoft.com/debian/12/prod bookworm main" \
    > /etc/apt/sources.list.d/mssql-release.list

# Cài ODBC driver
RUN apt-get update && ACCEPT_EULA=Y apt-get install -y msodbcsql18

# 👉 Cài PHP extension cần cho PECL
RUN docker-php-source extract \
    && pecl install sqlsrv pdo_sqlsrv \
    && docker-php-ext-enable sqlsrv pdo_sqlsrv \
    && docker-php-source delete

# Cài Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www