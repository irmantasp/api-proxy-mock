FROM php:7.4-fpm-alpine

COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY --from=symfonycorp/cli /symfony /usr/bin/symfony

WORKDIR /api-proxy-mock/

COPY . /api-proxy-mock/
RUN cp .env.build .env && rm .env.build

RUN composer install --no-interaction --no-plugins --no-scripts --no-dev --prefer-dist

RUN chmod -R 777 /api-proxy-mock/var

RUN chown www-data:www-data -R /api-proxy-mock

EXPOSE 8000
CMD ["symfony", "server:start", "--dir=/api-proxy-mock/public", "--port=8000", "-vvv"]
