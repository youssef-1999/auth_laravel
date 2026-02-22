FROM php:8.2-cli

WORKDIR /var/www

# install system dependencies
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip

# install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# copy project
COPY . .

# install dependencies
RUN composer install --no-dev --optimize-autoloader

# create sqlite database file
RUN mkdir -p database && touch database/database.sqlite

# generate key
RUN php artisan key:generate --force

EXPOSE 8000

CMD php artisan serve --host=0.0.0.0 --port=8000