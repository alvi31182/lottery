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
    librdkafka-dev \
    && docker-php-ext-configure tokenizer \
    && docker-php-ext-configure pcntl --enable-pcntl \
    && docker-php-ext-configure intl --enable-intl \
    && docker-php-ext-install -j$(nproc) pcntl xmlwriter simplexml intl mysqli pdo pdo_pgsql sockets \
    && pecl update-channels \
    && pecl install apcu opcache rdkafka \
    && docker-php-ext-enable \
        rdkafka \
        pcntl \
        apcu \
        opcache \
        xmlwriter \
        simplexml

WORKDIR /var/www/lottery

COPY /docker/php-outbox-cli/outbox.sh /usr/src/app/outbox.sh

RUN chmod +x /usr/src/app/outbox.sh

CMD ["/bin/bash", "-c", "/usr/src/app/outbox.sh"]