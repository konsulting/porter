FROM php:7.0-cli

RUN apt-get update -y && \
    apt-get install -y --no-install-recommends \
        git zip unzip \
        curl \
        libfreetype6-dev libpq-dev libjpeg-dev libpng-dev libz-dev \
        libmagickwand-dev \
        libmemcached-dev \
        libxml2-dev \
        pdftk && \
    pecl install imagick xdebug memcached && \
    docker-php-ext-configure gd \
        --enable-gd-native-ttf \
        --with-freetype-dir=/usr/include/ \
        --with-jpeg-dir=/usr/include/ && \
    docker-php-ext-install \
        mbstring \
        pdo pdo_mysql pdo_pgsql mysqli \
        opcache \
        zip \
        gd \
        soap && \
    docker-php-ext-enable imagick xdebug memcached

# Add MailHogSend
RUN curl -sSL "https://github.com/mailhog/mhsendmail/releases/download/v0.2.0/mhsendmail_linux_amd64" -o /usr/local/bin/mhsendmail && \
    chmod +x /usr/local/bin/mhsendmail

RUN php -r "readfile('http://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer

RUN apt-get -y autoremove && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

WORKDIR /srv/app/
