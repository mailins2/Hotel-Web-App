#!/usr/bin/env sh
set -e

# KHỞI ĐỘNG CRON SERVICE
service cron start

cd /var/www

if [ -f composer.json ]; then
    if [ ! -f vendor/autoload.php ] \
        || [ composer.json -nt vendor/autoload.php ] \
        || { [ -f composer.lock ] && [ composer.lock -nt vendor/autoload.php ]; }; then
        echo "Installing PHP dependencies..."
        composer install --no-interaction --prefer-dist --no-progress
    fi
fi


exec "$@"
