#!/bin/sh

# Jalankan migrasi di sini (menggunakan user root saat startup)
echo "Running migrations..."
php artisan migrate --force

# Jalankan perintah utama untuk menjalankan PHP-FPM/Nginx
# Ini adalah perintah yang biasanya dijalankan oleh image serversideup
/usr/bin/supervisord -c /etc/supervisor/supervisord.conf