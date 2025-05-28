FROM php:8.2-fpm

RUN apt-get -y update && apt-get upgrade -y
RUN apt-get -y install libpq-dev zip unzip net-tools
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html/default
COPY . .
RUN set COMPOSER_ALLOW_SUPERUSER=1 && php /usr/bin/composer install

CMD ["php-fpm"]