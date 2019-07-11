FROM php:5.5-alpine

RUN docker-php-ext-install bcmath

RUN pecl channel-update pecl.php.net

RUN apk --update upgrade \
    && apk add  \
        autoconf \
        automake \
        make \
        gcc \
        g++

RUN pecl install xdebug-2.5.5 \
    && echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_enable=on" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_autostart=off" >> /usr/local/etc/php/conf.d/xdebug.ini

RUN echo date.timezone="UTC" > /usr/local/etc/php/conf.d/timezone.ini


COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR /app