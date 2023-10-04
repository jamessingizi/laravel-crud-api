FROM nginx:1.25.1 AS crud_api-deps
LABEL maintainer="jsingizi7@gmail.com"

# Let the container know that there is no tty
ENV DEBIAN_FRONTEND noninteractive
ENV php_conf /etc/php/8.2/fpm/php.ini
ENV fpm_conf /etc/php/8.2/fpm/pool.d/www.conf

ENV PM_MAX_CHILDREN 50
ENV PM_START_SERVERS 5
ENV PM_MIN_SPARE_SERVERS 5
ENV PM_MAX_SPARE_SERVERS 35

ENV COMPOSER_VERSION 2.5.2
#ENV  COMPOSER_ALLOW_SUPERUSER 1

ENV PIP_BREAK_SYSTEM_PACKAGES 1

# Get push tag
ARG COMMIT_TAG=prerelease
ENV APP_COMMIT_TAG=${COMMIT_TAG}

# Install Basic Requirements - https://hub.docker.com/r/wyveo/nginx-php-fpm/dockerfile
RUN set -x \
    && VERSION_CODENAME=$(cat /etc/os-release | grep "_CODEN" | cut -d '=' -f2) \
    && curl -L https://packages.sury.org/php/apt.gpg -o /etc/apt/trusted.gpg.d/php.gpg \
    && echo "deb https://packages.sury.org/php/ $VERSION_CODENAME main" > /etc/apt/sources.list.d/php.list \
    &&  apt-get update \
    && apt-get install -y nodejs npm \
    && apt-get install --no-install-recommends --no-install-suggests -q -y \
            apt-utils \
            nano \
            zip make\
            unzip \
            python3-pip \
            python3-setuptools \
            git \
            mariadb-client \
            php-xdebug\
            libmemcached-dev \
            libmemcached11 \
            libmagickwand-dev \
            php8.2-fpm \
            php8.2-cli \
            php8.2-bcmath \
            php8.2-dev \
            php8.2-common \
            php8.2-opcache \
            php8.2-readline \
            php8.2-mbstring \
            php8.2-curl \
            php8.2-gd \
            php8.2-imagick \
            php8.2-mysql \
            php8.2-zip \
            php8.2-pgsql \
            php8.2-intl \
            php8.2-xml \
            php8.2-sqlite3 \
            php-pear \
    && pecl -d php_suffix=8.2 install -o -f redis memcached \
    && mkdir -p /run/php \
    && pip3 install wheel \
    && pip3 install supervisor \
    # Fix: https://githubmemory.com/repo/coderanger/supervisor-stdout/issues/19
    && pip3 install install git+https://github.com/coderanger/supervisor-stdout \
    && echo "#!/bin/sh\nexit 0" > /usr/sbin/policy-rc.d \
    && rm -rf /etc/nginx/conf.d/default.conf \
    && sed -i -e "s/;cgi.fix_pathinfo=1/cgi.fix_pathinfo=0/g" ${php_conf} \
    && sed -i -e "s/memory_limit\s*=\s*.*/memory_limit = 256M/g" ${php_conf} \
    && sed -i -e "s/upload_max_filesize\s*=\s*2M/upload_max_filesize = 100M/g" ${php_conf} \
    && sed -i -e "s/post_max_size\s*=\s*8M/post_max_size = 100M/g" ${php_conf} \
    && sed -i -e "s/variables_order = \"GPCS\"/variables_order = \"EGPCS\"/g" ${php_conf} \
    && sed -i -e "s/;daemonize\s*=\s*yes/daemonize = no/g" /etc/php/8.2/fpm/php-fpm.conf \
    && sed -i -e "s/;catch_workers_output\s*=\s*yes/catch_workers_output = yes/g" ${fpm_conf} \
    && sed -i -e "s/pm.max_children = 5/pm.max_children = ${PM_MAX_CHILDREN}/g" ${fpm_conf} \
    && sed -i -e "s/pm.start_servers = 2/pm.start_servers = ${PM_START_SERVERS}/g" ${fpm_conf} \
    && sed -i -e "s/pm.min_spare_servers = 1/pm.min_spare_servers = ${PM_MIN_SPARE_SERVERS}/g" ${fpm_conf} \
    && sed -i -e "s/pm.max_spare_servers = 3/pm.max_spare_servers = ${PM_MAX_SPARE_SERVERS}/g" ${fpm_conf} \
    && sed -i -e "s/pm.max_requests = 500/pm.max_requests = 200/g" ${fpm_conf} \
    && sed -i -e "s/www-data/nginx/g" ${fpm_conf} \
    && sed -i -e "s/^;clear_env = no$/clear_env = no/" ${fpm_conf} \
    && echo "extension=redis.so" > /etc/php/8.2/mods-available/redis.ini \
    && echo "extension=memcached.so" > /etc/php/8.2/mods-available/memcached.ini \
    && echo "extension=imagick.so" > /etc/php/8.2/mods-available/imagick.ini \
    && ln -sf /etc/php/8.2/mods-available/redis.ini /etc/php/8.2/fpm/conf.d/20-redis.ini \
    && ln -sf /etc/php/8.2/mods-available/redis.ini /etc/php/8.2/cli/conf.d/20-redis.ini \
    && ln -sf /etc/php/8.2/mods-available/memcached.ini /etc/php/8.2/fpm/conf.d/20-memcached.ini \
    && ln -sf /etc/php/8.2/mods-available/memcached.ini /etc/php/8.2/cli/conf.d/20-memcached.ini \
    && ln -sf /etc/php/8.2/mods-available/imagick.ini /etc/php/8.2/fpm/conf.d/20-imagick.ini \
    && ln -sf /etc/php/8.2/mods-available/imagick.ini /etc/php/8.2/cli/conf.d/20-imagick.ini \
    # Install Composer
    && curl -o /tmp/composer-setup.php https://getcomposer.org/installer \
    && curl -o /tmp/composer-setup.sig https://composer.github.io/installer.sig \
    && php -r "if (hash('SHA384', file_get_contents('/tmp/composer-setup.php')) !== trim(file_get_contents('/tmp/composer-setup.sig'))) { unlink('/tmp/composer-setup.php'); echo 'Invalid installer' . PHP_EOL; exit(1); }" \
    && php /tmp/composer-setup.php --no-ansi --install-dir=/usr/local/bin --filename=composer --version=${COMPOSER_VERSION} \
    && rm -rf /tmp/composer-setup.php \
    # Clean up
    && rm -rf /tmp/pear \
    && apt-get purge -y --auto-remove $buildDeps \
    && apt-get clean \
    && apt-get autoremove \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /usr/share/nginx/html

## Composer - https://dev.to/iacons/faster-docker-builds-with-composer-install-3opj
COPY composer.json ./

COPY . ./

RUN chown -Rf nginx:nginx /usr/share/nginx/html

# Install dependencies
RUN composer install --no-dev --no-interaction --optimize-autoloader \
    && rm -rf /root/.cache/composer

RUN php artisan config:cache

COPY ./supervisor/supervisord.conf /etc/supervisord.conf

# Override nginx's default config
COPY ./nginx/default.conf /etc/nginx/conf.d/default.conf

RUN composer dump-autoload

# Copy Scripts
COPY ./start.sh /start.sh

ENV APP_PORT 8080

EXPOSE ${APP_PORT}

ENTRYPOINT ["./entrypoint.sh"]

FROM crud_api-deps AS crud_api-test

# Install all dependencies including dev deps
RUN composer install --no-interaction --optimize-autoloader \
    && rm -rf /root/.cache/composer

HEALTHCHECK --interval=5s --timeout=10s --start-period=60s \
    CMD ["bash", "-c", "curl http://127.0.0.1:${APP_PORT}/ || exit 1"]

CMD ["/start.sh"]
