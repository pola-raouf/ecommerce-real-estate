# Stage 1: PHP + Composer
FROM php:8.2-fpm-alpine AS base

# Install system dependencies
RUN apk add --no-cache \
    bash \
    git \
    unzip \
    libpng libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    oniguruma-dev \
    curl \
    && docker-php-ext-install pdo_mysql mbstring zip exif pcntl gd

# Set working directory
WORKDIR /var/www/html

# Copy composer files
COPY composer.json composer.lock ./

# Install PHP dependencies
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && php -r "unlink('composer-setup.php');" \
    && composer install --no-dev --optimize-autoloader

# Copy app source
COPY . .

# Cache config & routes
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# Stage 2: Caddy for serving Laravel
FROM caddy:2.8.0-alpine

# Copy Laravel app from base
COPY --from=base /var/www/html /srv/app

# Set working directory
WORKDIR /srv/app

# Copy Caddyfile
COPY Caddyfile /etc/caddy/Caddyfile

# Expose port 8080 (Render default HTTP)
EXPOSE 8080

# Start Caddy
CMD ["caddy", "run", "--config", "/etc/caddy/Caddyfile", "--adapter", "caddyfile"]
