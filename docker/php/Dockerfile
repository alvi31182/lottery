FROM php:8.2-fpm AS aff_app

RUN apt-get update && apt-get install -y --no-install-recommends \
    libonig-dev \
    libicu-dev \
    libgmp-dev \
    libpq-dev \
    curl \
    libpng-dev \
    libwebp-dev \
    libjpeg-dev \
    libtidy-dev \
    libssl-dev \
    git-core \
    gcc \
    g++ \
    bash \
    libzip-dev \
    libxml2-dev \
    librdkafka-dev \
    && docker-php-ext-configure tokenizer \
    && docker-php-ext-configure pcntl --enable-pcntl \
    && docker-php-ext-configure intl --enable-intl \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install -j$(nproc) dom pcntl xmlwriter simplexml gd bcmath ctype fileinfo iconv intl mbstring mysqli pdo pdo_mysql pdo_pgsql sockets xml zip \
    && pecl update-channels \
    && pecl install xdebug apcu opcache sodium rdkafka \
    && docker-php-ext-enable \
        apcu \
        xdebug \
        opcache \
        sodium \
        xmlwriter \
        simplexml \
        rdkafka \
        pcntl \
    && mkdir /usr/so/ \
    && mkdir /usr/ini/ \
    && rm -rf /tmp/pear \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/lottery

COPY /docker/php/conf.d/xdebug.ini /usr/local/etc/php/conf.d