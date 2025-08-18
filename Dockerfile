FROM php:8.1-fpm as build

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app

COPY . .

RUN composer install

FROM php:8.1-cli

# RUN docker-php-ext-install mysqli

WORKDIR /app

COPY --from=build /app /app

EXPOSE 80

CMD ["php", "-S", "0.0.0.0:80", "-t", "/app"]
