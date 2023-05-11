FROM php:8.0-apache
RUN apt-get update 
RUN apt-get update && apt-get install -y libpq-dev && docker-php-ext-install pdo pdo_pgsql
RUN a2enmod rewrite       
COPY . /var/www/html
EXPOSE 8000