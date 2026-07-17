FROM php:8.4-fpm-alpine
RUN apk add --no-cache git icu-dev libpq-dev unzip \
 && docker-php-ext-install intl pdo_pgsql bcmath \
 && cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/backend
COPY backend/composer.json backend/composer.lock* ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist
COPY backend ./
RUN composer dump-autoload --optimize --no-dev
CMD ["php-fpm"]
