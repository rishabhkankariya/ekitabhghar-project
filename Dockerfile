FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd mysqli pdo pdo_mysql

RUN a2enmod rewrite

# Explicitly allow access
RUN echo '<Directory /var/www/html>\
    AllowOverride All\
    Require all granted\
</Directory>' >> /etc/apache2/apache2.conf