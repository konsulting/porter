FROM php:5.6-fpm

RUN docker-php-ext-install mbstring pdo pdo_mysql opcache

RUN apt-get update -y && apt-get install -y libpng-dev zlib1g-dev

RUN docker-php-ext-install mbstring zip gd

COPY storage/php_fpm_5-6/php.ini /usr/local/etc/php/php.ini

WORKDIR /srv/app/
