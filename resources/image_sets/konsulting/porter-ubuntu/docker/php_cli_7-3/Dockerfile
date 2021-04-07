#VERSION: 2.0.1
FROM ubuntu:20.04

RUN apt-get update \
    && apt-get install -y locales \
    && locale-gen en_US.UTF-8 \
    && apt-get autoremove -y \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

ENV DEBIAN_FRONTEND=noninteractive \
    LANG=en_US.UTF-8 \
    LANGUAGE=en_US:en \
    LC_ALL=en_US.UTF-8

RUN apt-get update \
    && apt-get install -y curl zip unzip git software-properties-common \
    && add-apt-repository -y ppa:ondrej/php \
    && apt-get install -y \
           php7.3-bcmath \
           php7.3-cli \
           php7.3-curl \
           php7.3-gd \
           php7.3-mysql \
           php7.3-pgsql \
           php7.3-imap \
           php7.3-imagick \
           php7.3-memcached \
           php7.3-mbstring \
           php7.3-opcache \
           php7.3-soap \
           php7.3-sqlite \
           php7.3-xdebug \
           php7.3-xml \
           php7.3-zip \
           libfontconfig1 libxrender1 \
           vim \
    && mkdir /run/php \
    && apt-get remove -y --purge software-properties-common \
    && apt-get autoremove -y \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN php -r "readfile('http://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer && \
    # Add MailHogSend
    curl -sSL "https://github.com/mailhog/mhsendmail/releases/download/v0.2.0/mhsendmail_linux_amd64" -o /usr/local/bin/mhsendmail && \
    chmod +x /usr/local/bin/mhsendmail

RUN sed -i -e "s|xdebug.so|/usr/lib/php/20180731/xdebug.so|" /etc/php/7.3/mods-available/xdebug.ini

# Install pdftk
# based on (https://gitlab.com/pdftk-java/pdftk)
RUN apt-get update \
        && apt-get -y install pdftk \
        && apt-get autoremove -y \
        && apt-get clean \
        && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Enable Imagick to work with PDFs
RUN sed -i -e 's/rights="none" pattern="PDF"/rights="read|write" pattern="PDF"/' /etc/ImageMagick-6/policy.xml

WORKDIR /srv/app

EXPOSE 8000
