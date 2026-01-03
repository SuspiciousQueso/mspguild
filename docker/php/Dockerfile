# docker/php/Dockerfile
FROM php:8.3-fpm
RUN set -eux; \
  apt-get update; \
  apt-get install -y --no-install-recommends \
    default-mysql-client \
  ; \
  docker-php-ext-install pdo_mysql mysqli; \
  rm -rf /var/lib/apt/lists/*

# set working dir
WORKDIR /var/www/

# copy composer.json earlier if present (speeds rebuild)
# COPY composer.json composer.lock ./
# RUN composer install --no-dev --no-autoloader || true

# permissions helper
RUN addgroup -g 1001 mspguild \
 && adduser -D -u 1001 -G mspguild mspguild
USER mspguild


EXPOSE 9000
CMD ["php-fpm"]
