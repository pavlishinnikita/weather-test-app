FROM php:8.2-fpm

RUN apt-get -y update && apt-get upgrade -y
RUN apt-get -y install libpq-dev zip unzip net-tools
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html/default

COPY composer.json composer.lock ./

RUN composer install --no-dev --optimize-autoloader --no-scripts --prefer-dist

COPY . .

RUN chown -R www-data:www-data public
RUN chmod -R 775 public

EXPOSE 9000

CMD ["php-fpm"]