FROM php:7.1-cli

RUN docker-php-ext-install mbstring pdo pdo_mysql opcache mysqli && \
    docker-php-ext-enable mysqli && \
    apt-get update -y && apt-get install -y libpng-dev zlib1g-dev git zip unzip && \
    docker-php-ext-install zip gd

RUN php -r "readfile('http://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer

RUN apt-get -y autoremove && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

WORKDIR /srv/app/
