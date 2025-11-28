# =====================
# 1️⃣ Base image
# =====================
FROM php:8.2-fpm

# =====================
# 2️⃣ System dependencies
# =====================
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libonig-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-install pdo_mysql mbstring zip exif pcntl gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# =====================
# 3️⃣ Set working directory
# =====================
WORKDIR /var/www/html

# =====================
# 4️⃣ Install Composer
# =====================
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && php -r "unlink('composer-setup.php');"

# =====================
# 5️⃣ Copy composer files & install PHP dependencies (without scripts to avoid artisan errors)
# =====================
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs --no-scripts

# =====================
# 6️⃣ Copy the rest of the application
# =====================
COPY . .

# =====================
# 7️⃣ Run post-install scripts now that the app is copied
# =====================
RUN composer run-script post-autoload-dump

# =====================
# 8️⃣ Copy or create .env file
# =====================
RUN cp .env.example .env || echo "No .env.example found, creating basic .env" && echo "APP_NAME=Laravel\nAPP_ENV=production\nAPP_KEY=\nAPP_DEBUG=false\nAPP_URL=https://your-app-name.onrender.com\nLOG_CHANNEL=stack\nDB_CONNECTION=mysql\nDB_HOST=127.0.0.1\nDB_PORT=3306\nDB_DATABASE=laravel\nDB_USERNAME=root\nDB_PASSWORD=\nBROADCAST_DRIVER=log\nCACHE_DRIVER=file\nFILESYSTEM_DRIVER=local\nQUEUE_CONNECTION=sync\nSESSION_DRIVER=file\nSESSION_LIFETIME=120\nMEMCACHED_HOST=127.0.0.1\nREDIS_HOST=127.0.0.1\nREDIS_PASSWORD=null\nREDIS_PORT=6379\nMAIL_MAILER=smtp\nMAIL_HOST=mailhog\nMAIL_PORT=1025\nMAIL_USERNAME=null\nMAIL_PASSWORD=null\nMAIL_ENCRYPTION=null\nMAIL_FROM_ADDRESS=null\nMAIL_FROM_NAME=\"${APP_NAME}\"\nAWS_ACCESS_KEY_ID=\nAWS_SECRET_ACCESS_KEY=\nAWS_DEFAULT_REGION=us-east-1\nAWS_BUCKET=\nAWS_USE_PATH_STYLE_ENDPOINT=false\nPUSHER_APP_ID=\nPUSHER_APP_KEY=\nPUSHER_APP_SECRET=\nPUSHER_APP_CLUSTER=mt1\nMIX_PUSHER_APP_KEY=\"${PUSHER_APP_KEY}\"\nMIX_PUSHER_APP_CLUSTER=\"${PUSHER_APP_CLUSTER}\"" > .env

# =====================
# 9️⃣ Set permissions
# =====================
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# =====================
# 10️⃣ Environment variables (override .env if needed)
# =====================
ENV APP_ENV=production
ENV APP_DEBUG=false
ENV APP_URL=https://your-app-name.onrender.com

# =====================
# 11️⃣ Generate app key if missing
# =====================
RUN php artisan key:generate

# =====================
# 🔥 Expose port and start PHP-FPM
# =====================
EXPOSE 10000
CMD ["php-fpm"]
