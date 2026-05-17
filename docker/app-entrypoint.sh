#!/usr/bin/env sh
set -e

service cron start

cd /var/www

if [ -f composer.json ]; then
    composer_marker="vendor/composer/installed.php"

    if [ ! -f vendor/autoload.php ] \
        || [ ! -f "$composer_marker" ] \
        || [ composer.json -nt "$composer_marker" ] \
        || { [ -f composer.lock ] && [ composer.lock -nt "$composer_marker" ]; }; then
        echo "Installing PHP dependencies..."
        composer install --no-interaction --prefer-dist --no-progress
    fi
fi

exec "$@"
