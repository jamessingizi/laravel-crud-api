#!/bin/bash
set -e

# Let the database start
while ! mysqladmin ping -h "$DB_HOST" -P "$DB_PORT" --silent; do
    echo "Connecting to ${DB_PORT} Failed"
    sleep 1
done

echo "Database is ready..."

# Ensure database is fully up to date
php artisan migrate --force

exec "$@"
