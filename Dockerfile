# Stage 1: Build Assets (Vite)
FROM node:20-alpine as assets-builder
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# Stage 2: PHP Application
FROM php:8.2-fpm-alpine

# Instal dependensi sistem dan ekstensi PHP yang dibutuhkan Laravel
RUN apk add --no-cache \
    nginx \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    zip \
    libzip-dev \
    unzip \
    git \
    curl \
    oniguruma-dev \
    libxml2-dev

RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath gd

# Instal Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Salin file proyek
COPY . .
COPY --from=assets-builder /app/public/build ./public/build

# Instal dependensi PHP
RUN composer install --no-dev --optimize-autoloader

# Atur izin folder storage dan bootstrap/cache
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
RUN chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Konfigurasi Nginx
COPY ./docker/nginx.conf /etc/nginx/http.d/default.conf

# Ekspos port 80
EXPOSE 80

# Jalankan Nginx dan PHP-FPM
CMD ["sh", "-c", "nginx && php-fpm"]
