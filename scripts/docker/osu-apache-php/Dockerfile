FROM php:5.6-apache

RUN apt-get update && apt-get install -y libyaml-dev \
    && pecl install yaml-1.3.1 \
    && docker-php-ext-install pdo_mysql \
    && a2enmod rewrite \
    && sed 's#;date.timezone =#date.timezone = America/Los_Angeles#' /usr/local/etc/php/php.ini-production \
        > /usr/local/etc/php/php.ini \
    && echo 'extension=yaml.so' >> /usr/local/etc/php/php.ini

WORKDIR /var/www/html

EXPOSE 80