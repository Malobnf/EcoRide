# Dockerfile
FROM php:8.3-apache

# Extensions PHP nécessaires
RUN docker-php-ext-install pdo pdo_mysql

# Activer mod_rewrite
RUN a2enmod rewrite

# Préciser la racine web pour Apache
ENV APACHE_DOCUMENT_ROOT=/var/www/html
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# Copier tout le projet
WORKDIR /var/www/html
COPY . .
