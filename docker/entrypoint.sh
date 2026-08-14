#!/bin/sh
set -e

# Port configuration (default to 8080 if not provided by Vercel environment)
PORT="${PORT:-8080}"
echo "Starting Flowra HTTP server on 0.0.0.0:${PORT}..."

# Replace __PORT__ in nginx configuration
sed -i "s/__PORT__/${PORT}/g" /etc/nginx/http.d/default.conf

# Ensure storage and bootstrap cache directories exist with appropriate permissions
mkdir -p /var/www/html/storage/framework/cache/data \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/logs \
         /var/www/html/bootstrap/cache

chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Start PHP-FPM daemon
php-fpm -D

# Start Nginx in foreground
exec nginx -g 'daemon off;'
