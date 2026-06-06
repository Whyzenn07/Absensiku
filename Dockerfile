FROM php:8.3-cli-alpine

# Install system dependencies + PHP extensions
RUN apk add --no-cache \
        git curl zip unzip \
        libpng-dev libxml2-dev libzip-dev oniguruma-dev \
    && docker-php-ext-install \
        pdo pdo_pgsql pdo_sqlite \
        gd mbstring xml zip bcmath pcntl

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy composer files first (layer cache)
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Copy all app files
COPY . .

# Run post-install scripts
RUN composer dump-autoload --optimize

# Permissions
RUN chmod -R 775 storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

# Expose port (Render sets $PORT)
EXPOSE 8080

# Entrypoint: migrate + seed + serve
CMD sh -c "\
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache && \
    php artisan migrate --force && \
    php artisan db:seed --force && \
    php -S 0.0.0.0:${PORT:-8080} -t public \
"
