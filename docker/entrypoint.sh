#!/usr/bin/env bash
set -e

# Support dynamic port binding on cloud platforms (Render, Railway)
if [ -n "$PORT" ]; then
    sed -i "s/Listen 80/Listen $PORT/g" /etc/apache2/ports.conf
    sed -i "s/:80/:$PORT/g" /etc/apache2/sites-available/000-default.conf
fi

# Ensure storage and cache directories exist and are writable
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/framework/cache
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/storage/app/public
mkdir -p /var/www/html/storage/app/documents/spmb
mkdir -p /var/www/html/database
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

# If using SQLite and file does not exist, initialize it
if [ "$DB_CONNECTION" = "sqlite" ]; then
    touch /var/www/html/database/database.sqlite
    chown www-data:www-data /var/www/html/database/database.sqlite
fi

# Create storage symlink
php artisan storage:link --force || true

# Run database migrations if configured
if [ "$RUN_MIGRATIONS" = "true" ] || [ "$APP_ENV" = "production" ]; then
    echo "Running database migrations..."
    php artisan migrate --force || true
fi

# Run database seeders if configured
if [ "$RUN_SEEDER" = "true" ]; then
    echo "Running database seeders..."
    php artisan db:seed --force || true
fi

# Optimize Laravel cache for production
echo "Caching Laravel configuration, routes, and views..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

echo "Starting Apache web server..."
exec apache2-foreground

