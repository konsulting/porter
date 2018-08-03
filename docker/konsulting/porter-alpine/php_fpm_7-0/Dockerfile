FROM alpine:3.5

RUN apk add --update --no-cache \
    curl \
    pdftk \
    php7-fpm \
    php7-bcmath \
    php7-bz2 \
    php7-curl \
    php7-common \
    php7-ctype \
    php7-dom \
    # php7-fileinfo \
    php7-ftp \
    php7-gd \
    php7-iconv \
    # php7-imagick \
    php7-imap \
    php7-intl \
    php7-json \
    php7-mbstring \
    php7-mcrypt \
    # php7-memcached \
    php7-mysqli \
    php7-mysqlnd \
    php7-opcache \
    php7-openssl \
    php7-pear \
    php7-pcntl \
    php7-pdo \
    php7-pdo_mysql \
    php7-pdo_pgsql \
    php7-pdo_sqlite \
    php7-session \
    # php7-simplexml \
    php7-soap \
    php7-sqlite3 \
    php7-tidy \
    # php7-tokenizer \
    php7-xdebug \
    php7-xml \
    php7-xmlrpc \
    # php7-xmlwriter \
    php7-zip \
    php7-zlib

RUN sed -i -e "s|listen\s*=.*|listen = 9000|" \
        -e "s|;clear_env = no|clear_env = no|" \
        /etc/php7/php-fpm.d/www.conf && \
    # Add MailHogSend
    curl -sSL "https://github.com/mailhog/mhsendmail/releases/download/v0.2.0/mhsendmail_linux_amd64" -o /usr/local/bin/mhsendmail && \
        chmod +x /usr/local/bin/mhsendmail

EXPOSE 9000

CMD ["php-fpm7", "-F"]

WORKDIR /srv/app
