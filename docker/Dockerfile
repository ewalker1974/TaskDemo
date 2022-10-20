FROM mdobak/php-common-stack:8.0edge-fpm-buster


RUN    docker-pcs-php-ext-install xdebug
           #xdebug.remote_host=docker.for.mac.localhost
RUN DEBIAN_FRONTEND=noninteractive apt-get update && apt-get install -y libc-client-dev libkrb5-dev dialog apt-utils
RUN docker-php-ext-configure imap --with-kerberos --with-imap-ssl \
    && docker-php-ext-install imap
RUN DEBIAN_FRONTEND=noninteractive apt-get -y install build-essential libssl-dev

RUN apt-get install -y  zlib1g zlib1g-dev libzip4 libzip-dev
RUN apt-get install -y  mc

RUN docker-pcs-php-ext-install zip pdo pdo_mysql

RUN apt-get install -y  libpng-dev
RUN docker-php-ext-install gd

ENV COMPOSER_ALLOW_SUPERUSER 1