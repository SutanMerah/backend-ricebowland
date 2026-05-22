FROM php:8.2-fpm-alpine

# Install ekstensi yang diperlukan Laravel, Nginx, & driver PostgreSQL (Supabase)
RUN apk add --no-cache nginx libpng-dev libzip-dev zip unzip postgresql-dev \
    && docker-php-ext-install pdo_mysql pdo_pgsql gd zip

# Konfigurasi Nginx
COPY nginx.conf /etc/nginx/nginx.conf

# Setup direktori aplikasi
WORKDIR /var/www/html
COPY . .

# Install Composer dan jalankan instalasi dependensi
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# Atur permission storage & cache Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# FIX: Berikan izin akses folder temporary Nginx agar tidak melempar error Permission Denied (13)
RUN chown -R www-data:www-data /var/lib/nginx /var/log/nginx && \
    chmod -R 755 /var/lib/nginx

# MEMAKSA NGINX MENGIRIM LOG KE LAYAR KONSOL FLY.IO
RUN ln -sf /dev/stdout /var/log/nginx/access.log \
    && ln -sf /dev/stderr /var/log/nginx/error.log

# Buka port 8080 agar sesuai dengan fly.toml
EXPOSE 8080

# Jalankan PHP-FPM di background, lalu jalankan Nginx di foreground
CMD php-fpm -D && nginx -g "daemon off;"