FROM php:8.2-apache

# Install mysqli
RUN docker-php-ext-install mysqli

# Enable Apache modules commonly needed
RUN a2enmod rewrite headers

# Copy app into the container
WORKDIR /var/www/html
COPY . /var/www/html

USER 1000
