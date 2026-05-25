#!/bin/bash

# Get PORT from environment or default to 10000
PORT=${PORT:-10000}

# Update Apache ports configuration
echo "Listen $PORT" > /etc/apache2/ports.conf

# Update VirtualHost configuration
sed -i "s/<VirtualHost \*:[0-9]*>/<VirtualHost *:$PORT>/" /etc/apache2/sites-available/000-default.conf

# Start Apache in foreground
apache2-foreground
