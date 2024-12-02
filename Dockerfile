FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    libpq-dev \
    unzip \
    curl

RUN docker-php-ext-install pdo_pgsql

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html

COPY . .

RUN composer install

RUN php artisan install:api || true

EXPOSE 8000

CMD php artisan serve --host=0.0.0.0 --port=8000
