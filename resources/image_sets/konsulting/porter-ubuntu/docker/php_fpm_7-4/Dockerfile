#VERSION: 2.0.1
FROM ubuntu:20.04

RUN apt-get update \
    && apt-get install -y locales \
    && locale-gen en_US.UTF-8 \
    && apt-get clean \
    && apt-get autoremove -y \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

ENV DEBIAN_FRONTEND=noninteractive \
    LANG=en_US.UTF-8 \
    LANGUAGE=en_US:en \
    LC_ALL=en_US.UTF-8

RUN apt-get update \
    && apt-get install -y curl zip unzip git software-properties-common \
    && add-apt-repository -y ppa:ondrej/php \
    && apt-get install -y \
          php7.4-fpm \
          php7.4-bcmath \
          php7.4-curl \
          php7.4-gd \
          php7.4-mysql \
          php7.4-pgsql \
          php7.4-imap \
          php7.4-imagick \
          php7.4-memcached \
          php7.4-mbstring \
          php7.4-opcache \
          php7.4-soap \
          php7.4-sqlite \
          php7.4-xdebug \
          php7.4-xml \
          php7.4-zip \
          libfontconfig1 libxrender1 \
    && mkdir /run/php \
    && apt-get remove -y --purge software-properties-common \
    && apt-get autoremove -y \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN sed -i -e "s|xdebug.so|/usr/lib/php/20190902/xdebug.so|" /etc/php/7.4/mods-available/xdebug.ini && \
    sed -i -e "s|listen\s*=.*|listen = 9000|" -e "s|;clear_env = no|clear_env = no|" /etc/php/7.4/fpm/pool.d/www.conf

# Add MailHogSend
RUN curl -sSL "https://github.com/mailhog/mhsendmail/releases/download/v0.2.0/mhsendmail_linux_amd64" -o /usr/local/bin/mhsendmail && \
        chmod +x /usr/local/bin/mhsendmail

# Install pdftk
# based on (https://gitlab.com/pdftk-java/pdftk)
RUN apt-get update \
        && apt-get -y install pdftk \
        && apt-get autoremove -y \
        && apt-get clean \
        && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Enable Imagick to work with PDFs
RUN sed -i -e 's/rights="none" pattern="PDF"/rights="read|write" pattern="PDF"/' /etc/ImageMagick-6/policy.xml

EXPOSE 9000

CMD ["php-fpm7.4", "-F"]

WORKDIR /srv/app
