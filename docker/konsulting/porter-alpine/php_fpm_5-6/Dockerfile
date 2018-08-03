FROM alpine:3.5

RUN apk add --update --no-cache \
    curl \
    pdftk \
    php5-fpm \
    php5-bcmath \
    php5-bz2 \
    php5-curl \
    php5-common \
    php5-ctype \
    php5-dom \
    # php5-fileinfo \
    php5-ftp \
    php5-gd \
    php5-iconv \
    php5-imagick \
    php5-imap \
    php5-intl \
    php5-json \
    # php5-mbstring \
    php5-mcrypt \
    # php5-memcached \
    php5-mysql \
    php5-mysqli \
    # php5-mysqlnd \
    php5-opcache \
    php5-openssl \
    php5-pear \
    php5-pcntl \
    php5-pdo \
    php5-pdo_mysql \
    php5-pdo_pgsql \
    php5-pdo_sqlite \
    # php5-session \
    # php5-simplexml \
    php5-soap \
    php5-sqlite3 \
    # php5-tidy \
    # php5-tokenizer \
    php5-xdebug \
    php5-xml \
    php5-xmlrpc \
    # php5-xmlwriter \
    php5-zip \
    php5-zlib

RUN sed -i -e "s|listen\s*=.*|listen = 9000|" /etc/php5/php-fpm.conf && \
    sed -i -e "s|;clear_env = no|clear_env = no|" /etc/php5/php-fpm.conf && \
    # Add MailHogSend
    curl -sSL "https://github.com/mailhog/mhsendmail/releases/download/v0.2.0/mhsendmail_linux_amd64" -o /usr/local/bin/mhsendmail && \
        chmod +x /usr/local/bin/mhsendmail

EXPOSE 9000

CMD ["php-fpm5", "-F"]

WORKDIR /srv/app
