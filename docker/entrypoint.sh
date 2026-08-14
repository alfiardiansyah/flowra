#!/bin/sh
set -e

# Port configuration (default to 8080 if not provided by Vercel environment)
PORT="${PORT:-8080}"
echo "Starting Flowra HTTP server on 0.0.0.0:${PORT}..."

# Generate Nginx configuration from template
mkdir -p /etc/nginx/http.d
sed "s/__PORT__/${PORT}/g" /var/www/html/docker/nginx.conf > /etc/nginx/http.d/default.conf

# Ensure storage and bootstrap cache directories exist with appropriate permissions
mkdir -p /var/www/html/storage/framework/cache/data \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/logs \
         /var/www/html/bootstrap/cache \
         /tmp/storage/framework/cache/data \
         /tmp/storage/framework/sessions \
         /tmp/storage/framework/views \
         /tmp/storage/logs \
         /tmp/bootstrap/cache

chmod -R 777 /tmp/storage /tmp/bootstrap 2>/dev/null || true
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /tmp/storage /tmp/bootstrap 2>/dev/null || true

# Test Nginx configuration
nginx -t

# Start PHP-FPM daemon
php-fpm -D

# Start Nginx in foreground
exec nginx -g 'daemon off;'
