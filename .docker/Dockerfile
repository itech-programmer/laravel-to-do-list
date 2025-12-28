FROM php:8.4-fpm

RUN apt-get update && apt-get install -y --no-install-recommends \
    git curl unzip default-mysql-client \
    libicu-dev libzip-dev \
    libonig-dev pkg-config \
 && docker-php-ext-install pdo pdo_mysql intl zip mbstring \
 && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

EXPOSE 9000
CMD ["php-fpm"]
