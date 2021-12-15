FROM php:7.4-apache

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

COPY . /api-proxy-mock/

WORKDIR /api-proxy-mock/

RUN cp .env.build .env
RUN rm .env.build

RUN sed -ri -e 's!/var/www/html!/api-proxy-mock/public!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!/api-proxy-mock/public!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

RUN composer install --no-interaction --no-plugins --no-scripts --no-dev --prefer-dist
