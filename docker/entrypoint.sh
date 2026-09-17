#!/bin/bash
set -e

# Dynamically bind Apache port to $PORT (Render or Koyeb dynamically assigns PORT, default 80)
PORT=${PORT:-80}
echo "[StayFinder] Configuring Apache to listen on port ${PORT}..."
sed -i "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost \*:${PORT}>/g" /etc/apache2/sites-available/000-default.conf

# SQLite database setup if DB_CONNECTION is sqlite
if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ]; then
    mkdir -p /var/www/html/database
    if [ ! -f /var/www/html/database/database.sqlite ]; then
        echo "[StayFinder] Creating fresh database.sqlite file..."
        touch /var/www/html/database/database.sqlite
    fi
    chmod -R 777 /var/www/html/database
fi

# Ensure storage directories exist with proper permissions
mkdir -p /var/www/html/storage/framework/{sessions,views,cache}
mkdir -p /var/www/html/storage/logs
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Generate application key if not set
if [ -z "$APP_KEY" ]; then
    echo "[StayFinder] No APP_KEY provided in environment. Generating new application key..."
    php artisan key:generate --force
fi

# Create storage symbolic link
php artisan storage:link --force || true

# Run database migrations
echo "[StayFinder] Running database migrations..."
php artisan migrate --force

# Seed database
echo "[StayFinder] Running database seeders..."
php artisan db:seed --force || true

# Cache configurations, routes, and views for lightning fast production response
echo "[StayFinder] Optimizing caches for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "[StayFinder] Application ready! Starting Apache web server on port ${PORT}..."
exec apache2-foreground
