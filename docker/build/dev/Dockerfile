###### PHP
FROM php:8.2-fpm AS fpm
ENV REFRESHED_AT 2023-04-09

RUN apt-get update && \
    apt-get install -y \
    git \
    zip \
    unzip \
    libzip-dev \
    libpq-dev \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install \
    zip \
    pdo \
    pdo_pgsql \
    && rm -rf /tmp/* /var/tmp/* /var/cache/*

RUN pecl install mongodb-1.15.3 && docker-php-ext-enable mongodb

COPY --from=composer:2.5.5 /usr/bin/composer /usr/bin/composer
VOLUME /code
WORKDIR /code

# Xdebug extension
RUN pecl install xdebug-3.2.1 && docker-php-ext-enable xdebug
COPY ./docker/build/dev/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

###### Nginx
FROM nginx:1.15 AS nginx
COPY ./docker/build/dev/nginx.conf /etc/nginx/conf.d/default.conf
VOLUME /code
WORKDIR /code
