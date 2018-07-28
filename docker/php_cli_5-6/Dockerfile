FROM php:5.6-cli

RUN docker-php-ext-install mbstring pdo pdo_mysql opcache

RUN apt-get update -y && apt-get install -y libpng-dev zlib1g-dev

RUN docker-php-ext-install mbstring zip gd

RUN apt-get update && \
    apt-get install -y git zip unzip && \
    php -r "readfile('http://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer && \
    apt-get -y autoremove && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

COPY storage/php_fpm_56/php.ini /usr/local/etc/php/php.ini

WORKDIR /srv/app/
