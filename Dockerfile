FROM php:8.2-apache

# Set working directory
WORKDIR /var/www/html

# Copy project files into container
COPY . .

# Install GD + MySQL extensions
RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd mysqli pdo pdo_mysql

# Enable mod_rewrite
RUN a2enmod rewrite