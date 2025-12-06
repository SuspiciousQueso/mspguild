# docker/php/Dockerfile
FROM php:8.3-fpm

# system deps
RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip git libpng-dev libonig-dev libxml2-dev libssl-dev \
    && rm -rf /var/lib/apt/lists/*

# php extensions
RUN docker-php-ext-install pdo pdo_mysql mysqli mbstring zip exif pcntl bcmath sockets xml opcache
RUN docker-php-ext-configure gd --with-jpeg && docker-php-ext-install gd || true

# intl (optional), requires additional libs (skip if heavy)
# RUN apt-get update && apt-get install -y libicu-dev && docker-php-ext-install intl

# composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# set working dir
WORKDIR /var/www/html

# copy composer.json earlier if present (speeds rebuild)
# COPY composer.json composer.lock ./
# RUN composer install --no-dev --no-autoloader || true

# permissions helper
RUN useradd -m appuser || true
USER appuser

EXPOSE 9000
CMD ["php-fpm"]
