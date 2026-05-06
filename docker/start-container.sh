#!/bin/sh
set -eu

cd /var/www/html

mkdir -p storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

if [ -n "${MYSQL_ATTR_SSL_CA_CONTENT:-}" ]; then
    mkdir -p storage/certs
    printf '%s\n' "$MYSQL_ATTR_SSL_CA_CONTENT" > storage/certs/aiven-ca.pem
    chown www-data:www-data storage/certs/aiven-ca.pem
    chmod 644 storage/certs/aiven-ca.pem
    export MYSQL_ATTR_SSL_CA=/var/www/html/storage/certs/aiven-ca.pem
fi

php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear || true
php artisan storage:link || true
php artisan migrate --force

exec apache2-foreground
