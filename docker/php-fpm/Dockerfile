# docker/php-fpm/Dockerfile
FROM php:7.4-fpm-alpine3.10

ENV DEBIAN_FRONTEND noninteractive

RUN apk add tzdata && cp /usr/share/zoneinfo/Asia/Tokyo /etc/localtime \
    && echo "Asia/Tokyo" >  /etc/timezone

RUN apk upgrade --update && apk --no-cache add git libzip-dev zip unzip autoconf gcc g++ make \
    freetype-dev libjpeg libjpeg-turbo-dev libpng-dev pcre-dev \
    && apk add --no-cache --virtual .build-deps \
        make \
        gcc \
        g++ \
    && curl -SL http://ftp.gnu.org/pub/gnu/libiconv/libiconv-1.15.tar.gz | tar -xz -C ~/ \
    && rm /usr/bin/iconv \
    && mv ~/libiconv-1.15 ~/libiconv \
    && ~/libiconv/configure --prefix=/usr/bin \
    && make \
    && make install \
    && rm -Rf ~/libiconv \
    && apk del .build-deps

ENV LD_PRELOAD /usr/bin/lib/preloadable_libiconv.so

RUN docker-php-ext-install zip pdo_mysql opcache gd

RUN pecl install redis apcu && \
    docker-php-ext-enable redis apcu

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

# node.js install
RUN curl -sL https://deb.nodesource.com/setup_13.x | php -- --install-dir=/usr/bin
RUN apk add --update nodejs nodejs-npm

RUN { \
    # echo 'memory_limit=-1'; \
    echo 'upload_max_filesize = 100M'; \
    echo 'post_max_size = 108M'; \
    echo 'short_open_tag = On'; \
    echo 'fastcgi.logging = 1'; \
    echo 'opcache.enable=1'; \
    echo 'opcache.optimization_level=0x7FFFBBFF'; \
    echo 'opcache.validate_timestamps=1'; \
    echo 'opcache.memory_consumption=128'; \
    echo 'opcache.interned_strings_buffer=8'; \
    echo 'opcache.max_accelerated_files=4000'; \
    echo 'opcache.revalidate_freq=60'; \
    echo 'opcache.fast_shutdown=1'; \
    echo 'xdebug.remote_enable=1'; \
} > /usr/local/etc/php/conf.d/overrides.ini

RUN { \
    echo 'apc.shm_segments=1'; \
    echo 'apc.shm_size=512M'; \
    echo 'apc.num_files_hint=7000'; \
    echo 'apc.user_entries_hint=4096'; \
    echo 'apc.ttl=7200'; \
    echo 'apc.user_ttl=7200'; \
    echo 'apc.gc_ttl=3600'; \
    echo 'apc.max_file_size=1M'; \
    echo 'apc.stat=1'; \
} > /usr/local/etc/php/conf.d/overrides_apcu.ini

# RUN mkdir -p /app
COPY . /var/www
# COPY .env.development /var/www/.env

WORKDIR /var/www

# RUN composer install
# RUN cp .env.example .env
# RUN cp .env .env
# RUN php artisan key:generate
# RUN chmod -R a+w storage/ bootstrap/cache
