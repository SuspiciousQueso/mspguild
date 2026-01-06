# docker/php/Dockerfile
FROM php:8.3-fpm
RUN set -eux; \
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip \
    && rm -rf /var/lib/apt/lists/*
# ---- Install Composer ----
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
# set working dir
WORKDIR /var/www/

# copy composer.json earlier if present (speeds rebuild)
# COPY composer.json composer.lock ./
# RUN composer install --no-dev --no-autoloader || true

# permissions helper
#RUN addgroup -g 1000 mspguild \
# && adduser -D -u 1000 -G mspguild mspguild
#USER mspguild


EXPOSE 9000
CMD ["php-fpm"]
