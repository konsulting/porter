FROM php:7.1-fpm

RUN docker-php-ext-install mbstring pdo pdo_mysql opcache

RUN apt-get update -y && apt-get install -y libpng-dev zlib1g-dev

RUN docker-php-ext-install mbstring zip gd

WORKDIR /srv/app/
