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
    libcurl4-openssl-dev \
    pkg-config \
    libssl-dev \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install \
    zip \
    pdo \
    pdo_pgsql \
    && rm -rf /tmp/* /var/tmp/* /var/cache/*

RUN pecl install mongodb-1.15.3 && docker-php-ext-enable mongodb
COPY --from=composer:2.5.5 /usr/bin/composer /usr/bin/composer

WORKDIR /code

ENV COMPOSER_ALLOW_SUPERUSER=1
COPY ./composer.json /code/composer.json
COPY ./composer.lock /code/composer.lock
RUN composer install --no-scripts
COPY . /code
RUN ./bin/console cache:clear -e prod
RUN ./bin/console cache:warmup -e prod
RUN chmod -R 777 ./var/cache/
RUN chmod -R 777 ./var/log/

FROM fpm AS fpm-prod

FROM fpm AS fpm-dev
# Xdebug extension
RUN pecl install xdebug-3.2.1 && docker-php-ext-enable xdebug
COPY ./docker/build/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini
