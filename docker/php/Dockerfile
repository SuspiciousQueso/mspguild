# docker/php/Dockerfile
FROM php:8.3-fpm

# set working dir
WORKDIR /var/www/

# copy composer.json earlier if present (speeds rebuild)
# COPY composer.json composer.lock ./
# RUN composer install --no-dev --no-autoloader || true

# permissions helper
RUN addgroup -g 1000 mspguild \
 && adduser -D -u 1000 -G mspguild mspguild
USER mspguild


EXPOSE 9000
CMD ["php-fpm"]
