#VERSION: 1.2.0
FROM ubuntu:18.04

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
          php7.2-fpm \
          php7.2-bcmath \
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
    && mkdir /run/php \
    && apt-get remove -y --purge software-properties-common \
    && apt-get autoremove -y \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN sed -i -e "s|xdebug.so|/usr/lib/php/20170718/xdebug.so|" /etc/php/7.2/mods-available/xdebug.ini && \
    sed -i -e "s|listen\s*=.*|listen = 9000|" -e "s|;clear_env = no|clear_env = no|" /etc/php/7.2/fpm/pool.d/www.conf

# Add MailHogSend
RUN curl -sSL "https://github.com/mailhog/mhsendmail/releases/download/v0.2.0/mhsendmail_linux_amd64" -o /usr/local/bin/mhsendmail && \
        chmod +x /usr/local/bin/mhsendmail

# Install pdftk
# based on (https://askubuntu.com/a/1046476)
RUN curl -OL http://mirrors.kernel.org/ubuntu/pool/main/g/gcc-6/libgcj17_6.4.0-8ubuntu1_amd64.deb \
         -OL http://mirrors.kernel.org/ubuntu/pool/main/g/gcc-defaults/libgcj-common_6.4-3ubuntu1_all.deb \
         -OL http://mirrors.kernel.org/ubuntu/pool/universe/p/pdftk/pdftk_2.02-4build1_amd64.deb \
         -OL http://mirrors.kernel.org/ubuntu/pool/universe/p/pdftk/pdftk-dbg_2.02-4build1_amd64.deb \
    && apt-get update \
    && apt-get install -y ./libgcj17_6.4.0-8ubuntu1_amd64.deb \
        ./libgcj-common_6.4-3ubuntu1_all.deb \
        ./pdftk_2.02-4build1_amd64.deb \
        ./pdftk-dbg_2.02-4build1_amd64.deb \
    && rm ./libgcj17_6.4.0-8ubuntu1_amd64.deb \
        ./libgcj-common_6.4-3ubuntu1_all.deb \
        ./pdftk_2.02-4build1_amd64.deb  \
        ./pdftk-dbg_2.02-4build1_amd64.deb \
    && apt-get remove -y --purge software-properties-common \
        && apt-get autoremove -y \
        && apt-get clean \
        && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

EXPOSE 9000

CMD ["php-fpm7.2", "-F"]

WORKDIR /srv/app
