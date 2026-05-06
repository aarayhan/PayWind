#!/bin/sh
set -eu

cd /var/www/html

mkdir -p storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear || true
php artisan storage:link || true
php artisan migrate --force

exec apache2-foreground
