FROM php:8.1-fpm

WORKDIR /omniapi
COPY . /omniapi

RUN apt-get update && \
    apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev zip unzip && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd pdo pdo_mysql && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

# RUN pecl install -o -f redis \
#     && rm -rf /tmp/pear \
#     && docker-php-ext-enable redis

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY composer.json composer.lock ./

RUN composer install --no-scripts --no-autoloader

RUN composer dump-autoload

RUN chmod -R 777 /omniapi/storage

ENTRYPOINT ["./entrypoint.sh"]

RUN groupadd -g 1000 tmpnp_usr
RUN useradd -u 1000 -ms /bin/bash -g tmpnp_usr tmpnp_usr

COPY --chown=tmpnp_usr:tmpnp_usr . /omniapi/

COPY package.json package-lock.json ./

USER tmpnp_usr

EXPOSE 9000

CMD ["php-fpm"]