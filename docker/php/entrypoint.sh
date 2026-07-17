#!/bin/sh
set -eu

mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs

if [ ! -f vendor/autoload.php ]; then
    echo "Installing backend dependencies..."
    composer install --no-interaction --prefer-dist
fi

exec "$@"
