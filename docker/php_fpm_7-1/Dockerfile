FROM php:7.1-fpm

RUN docker-php-ext-install mbstring pdo pdo_mysql opcache mysqli

RUN apt-get update -y && apt-get install -y libpng-dev zlib1g-dev

RUN docker-php-ext-install mbstring zip gd

RUN docker-php-ext-enable mysqli

WORKDIR /srv/app/
