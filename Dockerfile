FROM php:7.4-cli-alpine

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

WORKDIR /api-proxy-mock/

COPY . /api-proxy-mock/
RUN cp .env.build .env
RUN rm .env.build

RUN composer install --no-interaction --no-plugins --no-scripts --no-dev --prefer-dist

EXPOSE 8000

CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
