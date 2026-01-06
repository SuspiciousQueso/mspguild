# docker/php/Dockerfile
FROM php:8.3-fpm
#RUN set -eux; \
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
EXPOSE 9000
CMD ["php-fpm"]
