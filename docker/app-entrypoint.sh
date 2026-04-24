#!/usr/bin/env sh
set -e

cd /var/www

if [ -f composer.json ]; then
    if [ ! -f vendor/autoload.php ] \
        || [ composer.json -nt vendor/autoload.php ] \
        || { [ -f composer.lock ] && [ composer.lock -nt vendor/autoload.php ]; }; then
        echo "Installing PHP dependencies..."
        composer install --no-interaction --prefer-dist --no-progress
    fi
fi

# if [ -f package.json ]; then
#     if [ ! -d node_modules ] \
#         || [ package.json -nt node_modules ] \
#         || { [ -f package-lock.json ] && [ package-lock.json -nt node_modules ]; }; then
#         echo "Installing Node dependencies..."
#         if [ -f package-lock.json ]; then
#             npm ci
#         else
#             npm install
#         fi
#     fi
# fi

exec "$@"
