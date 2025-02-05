FROM php:8.4-fpm

ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

ENV LANG=en_US.UTF-8 \
    LANGUAGE=en_US.UTF-8 \
    LC_ALL=en_US.UTF-8 \
    XDG_CONFIG_HOME=/tmp \
    COMPOSER_HOME=/tmp/.composer

RUN set -euxv \
    && apt-get update \
    && apt-get install -y --no-install-recommends \
        locales \
    && sed -i -e 's/# en_US.UTF-8 UTF-8/en_US.UTF-8 UTF-8/' /etc/locale.gen \
    && locale-gen \
    && install-php-extensions \
        pdo_mysql \
        redis \
        sockets \
        pcntl \
        uv \
        xdebug \
        @composer \
    && rm /usr/local/bin/install-php-extensions \
    && echo "xdebug.client_host = host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && { \
        echo '[global]'; \
        echo 'daemonize = no'; \
        echo 'error_log = /proc/self/fd/2'; \
        echo; \
        echo '[www]'; \
        echo 'listen = [::]:9000'; \
        echo 'listen.owner = www-data'; \
        echo 'listen.group = www-data'; \
        echo; \
        echo 'user = www-data'; \
        echo 'group = www-data'; \
        echo; \
        echo 'access.log = /proc/self/fd/2'; \
        echo; \
        echo 'pm = dynamic'; \
        echo 'pm.max_children = 512'; \
        echo 'pm.start_servers = 16'; \
        echo 'pm.min_spare_servers = 16'; \
        echo 'pm.max_spare_servers = 256'; \
        echo 'pm.max_requests = 256'; \
        echo 'request_terminate_timeout = 300s'; \
        echo 'catch_workers_output = yes'; \
    } | tee /usr/local/etc/php-fpm.d/www.conf \
    && mv $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini \
    && rm -r /tmp/.composer

COPY php.ini /usr/local/etc/php/conf.d/

WORKDIR /app

USER www-data:www-data
