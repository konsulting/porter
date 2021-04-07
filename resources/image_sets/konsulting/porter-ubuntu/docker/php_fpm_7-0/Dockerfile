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
          php7.0-fpm \
          php7.0-bcmath \
          php7.0-curl \
          php7.0-gd \
          php7.0-mysql \
          php7.0-pgsql \
          php7.0-imap \
          php7.0-imagick \
          php7.0-memcached \
          php7.0-mbstring \
          php7.0-opcache \
          php7.0-soap \
          php7.0-sqlite \
          php7.0-xdebug \
          php7.0-xml \
          php7.0-zip \
          libfontconfig1 libxrender1 \
    && mkdir /run/php \
    && apt-get remove -y --purge software-properties-common \
    && apt-get autoremove -y \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN sed -i -e "s|xdebug.so|/usr/lib/php/20151012/xdebug.so|" /etc/php/7.0/mods-available/xdebug.ini && \
    sed -i -e "s|listen\s*=.*|listen = 9000|" -e "s|;clear_env = no|clear_env = no|" /etc/php/7.0/fpm/pool.d/www.conf

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

CMD ["php-fpm7.0", "-F"]

WORKDIR /srv/app
