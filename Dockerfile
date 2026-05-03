FROM php:8.2-apache

WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libpq-dev

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd mysqli pdo pdo_mysql pdo_pgsql pgsql

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy project files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Enable rewrite
RUN a2enmod rewrite

# Configure Apache to listen on PORT environment variable
RUN echo 'Listen ${PORT}' > /etc/apache2/ports.conf

# Create a simple startup script
RUN echo '#!/bin/bash\n\
export PORT=${PORT:-80}\n\
echo "Starting Apache on port $PORT"\n\
sed -i "s/Listen 80/Listen $PORT/g" /etc/apache2/ports.conf\n\
sed -i "s/:80/:$PORT/g" /etc/apache2/sites-available/000-default.conf\n\
apache2-foreground' > /start.sh && chmod +x /start.sh

# Expose port
EXPOSE $PORT

# Start Apache with custom script
CMD ["/start.sh"]