#!/usr/bin/env bash
# Keluar dari skrip jika ada error
set -o errexit

# Install dependensi composer untuk production
composer install --no-dev --optimize-autoloader

# Jalankan migrasi database otomatis ke PostgreSQL Render
php artisan migrate --force