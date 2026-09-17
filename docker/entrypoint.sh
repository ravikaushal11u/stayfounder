#!/bin/bash
set -e

# Dynamically bind Apache port to $PORT (Render assigns PORT e.g. 10000, default 80)
PORT=${PORT:-80}
echo "[StayFinder] Configuring Apache to listen on port ${PORT}..."
sed -i "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost \*:${PORT}>/g" /etc/apache2/sites-available/000-default.conf

# 1. Ensure .env exists and is writable
if [ ! -f /var/www/html/.env ]; then
    echo "[StayFinder] Initializing .env from .env.example..."
    cp /var/www/html/.env.example /var/www/html/.env
fi
chmod 666 /var/www/html/.env

# 2. Ensure APP_KEY is valid
if [ -n "$APP_KEY" ]; then
    echo "[StayFinder] Applying APP_KEY from environment variable..."
    sed -i "s|^APP_KEY=.*|APP_KEY=${APP_KEY}|g" /var/www/html/.env
elif ! grep -q "^APP_KEY=base64:" /var/www/html/.env; then
    echo "[StayFinder] Generating application key..."
    php artisan key:generate --force
fi

# 3. SQLite database setup
if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ]; then
    mkdir -p /var/www/html/database
    if [ ! -f /var/www/html/database/database.sqlite ]; then
        echo "[StayFinder] Creating fresh database.sqlite file..."
        touch /var/www/html/database/database.sqlite
    fi
    chown -R www-data:www-data /var/www/html/database
    chmod -R 777 /var/www/html/database
fi

# 4. Ensure storage directories exist with proper permissions
mkdir -p /var/www/html/storage/framework/{sessions,views,cache}
mkdir -p /var/www/html/storage/logs
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 5. Create storage symbolic link
php artisan storage:link --force || true

# 6. Run database migrations
echo "[StayFinder] Running database migrations..."
php artisan migrate --force

# 7. Seed database with demo data
echo "[StayFinder] Running database seeders..."
php artisan db:seed --force || true

# 8. Cache configurations, routes, and views for production
echo "[StayFinder] Optimizing caches for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "[StayFinder] Application ready! Starting Apache web server on port ${PORT}..."
exec apache2-foreground
