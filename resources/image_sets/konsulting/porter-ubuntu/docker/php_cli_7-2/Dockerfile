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
           php7.2-bcmath \
           php7.2-cli \
           php7.2-curl \
           php7.2-gd \
           php7.2-mysql \
           php7.2-pgsql \
           php7.2-imap \
           php7.2-imagick \
           php7.2-memcached \
           php7.2-mbstring \
           php7.2-opcache \
           php7.2-soap \
           php7.2-sqlite \
           php7.2-xdebug \
           php7.2-xml \
           php7.2-zip \
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

RUN sed -i -e "s|xdebug.so|/usr/lib/php/20170718/xdebug.so|" /etc/php/7.2/mods-available/xdebug.ini

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
