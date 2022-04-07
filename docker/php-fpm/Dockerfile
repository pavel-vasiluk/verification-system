FROM php:8.1-fpm

# Set Environment Variables
ENV DEBIAN_FRONTEND noninteractive

RUN set -eux; \
    apt-get update; \
    apt-get upgrade -y; \
    apt-get install -y --no-install-recommends \
            curl

RUN set -eux; \
    # Install the PHP pdo_mysql extension
    docker-php-ext-install pdo_mysql;

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN chown www-data:www-data /var/www

USER www-data

WORKDIR /var/www

EXPOSE 9000
CMD ["php-fpm"]