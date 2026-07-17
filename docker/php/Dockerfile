FROM php:8.4-fpm-alpine AS php-base

RUN apk add --no-cache fcgi icu-dev libpq-dev linux-headers \
    && docker-php-ext-install -j"$(nproc)" bcmath intl opcache pcntl pdo_pgsql \
    && cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY docker/php/zz-health.conf /usr/local/etc/php-fpm.d/zz-health.conf
WORKDIR /var/www/backend

FROM php-base AS development
RUN cp "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
COPY docker/php/entrypoint.sh /usr/local/bin/anil-entrypoint
RUN chmod +x /usr/local/bin/anil-entrypoint
ENTRYPOINT ["anil-entrypoint"]
CMD ["php-fpm"]

FROM php-base AS production-dependencies
COPY backend/composer.json backend/composer.lock* ./
RUN composer install --no-dev --no-interaction --no-scripts --no-autoloader --prefer-dist
COPY backend ./
RUN composer dump-autoload --classmap-authoritative --no-dev \
    && php artisan package:discover --ansi

FROM php-base AS production
COPY --from=production-dependencies --chown=www-data:www-data /var/www/backend /var/www/backend
USER www-data
CMD ["php-fpm"]
