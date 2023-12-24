FROM php:8.2-cli AS aff_app_cli

RUN apt-get update && apt-get install -y --no-install-recommends \
    curl \
    bash \
    libpq-dev \
    g++ \
    libxml2-dev \
    tzdata \
    dumb-init \
    fcgiwrap \
    && docker-php-ext-configure tokenizer \
    && docker-php-ext-configure pcntl --enable-pcntl \
    && docker-php-ext-configure intl --enable-intl \
    && docker-php-ext-install -j$(nproc) pcntl xmlwriter simplexml intl mysqli pdo pdo_pgsql sockets \
    && pecl update-channels \
    && pecl install apcu opcache \
    && docker-php-ext-enable \
        pcntl \
        apcu \
        opcache \
        xmlwriter \
        simplexml

WORKDIR /var/www/lottery

COPY /docker/php-cli/start_lottery.sh /usr/src/app/start_lottery.sh

RUN chmod +x /usr/src/app/start_lottery.sh

CMD ["/bin/bash", "-c", "/usr/src/app/start_lottery.sh"]