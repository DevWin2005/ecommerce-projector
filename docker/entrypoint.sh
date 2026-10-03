#!/bin/sh
set -e

echo "==> Running pre-flight checks..."
php /var/www/html/docker/check-ca.php

echo "==> Setting directory permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "==> Optimizing configuration..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo "==> Running database migrations..."
php artisan migrate --force

if [ "$RUN_SEEDER" = "true" ]; then
    echo "==> Running database seeders..."
    php artisan db:seed --force
fi

echo "==> Caching routes and configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

if [ -n "$PORT" ]; then
    sed -i "s/listen 80;/listen $PORT;/g" /etc/nginx/http.d/default.conf
fi

echo "==> Starting web server..."
exec "$@"