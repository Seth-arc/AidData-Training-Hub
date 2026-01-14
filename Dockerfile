FROM php:8.0-fpm

# Install apt deps and PHP extensions WordPress needs
RUN apt-get update \
 && apt-get install -y nginx unzip libzip-dev libpng-dev libonig-dev libxml2-dev libjpeg62-turbo-dev gettext-base \
 && docker-php-ext-install mysqli pdo pdo_mysql zip gd

# Copy WordPress code
WORKDIR /var/www/html
COPY app/public/ .

# Set up Nginx
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/site.conf /etc/nginx/conf.d/default.conf

# PHP-FPM listens on 9000; expose via Nginx on 3000 for Railway
ENV PORT=3000
EXPOSE 3000

# Entrypoint script to start php-fpm + nginx
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh
CMD ["/entrypoint.sh"]