# ==============================================================================
# Folium SIGB Backend — Dockerfile (PHP 8.4 + Laravel 12 API)
# ==============================================================================
FROM php:8.4-cli-alpine

# Install system dependencies & PHP extensions required by Laravel & MySQL/SQLite
RUN apk add --no-cache \
    bash \
    curl \
    git \
    icu-dev \
    libzip-dev \
    oniguruma-dev \
    sqlite-dev \
    unzip \
    && docker-php-ext-install \
    bcmath \
    intl \
    mbstring \
    pcntl \
    pdo \
    pdo_mysql \
    pdo_sqlite \
    zip

# Copy Composer from official image
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy composer files first to leverage Docker layer cache
COPY composer.json composer.lock ./

RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-scripts

# Copy application source
COPY . .

# Ensure storage & cache directories exist and are writable
RUN mkdir -p storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]

