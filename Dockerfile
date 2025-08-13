FROM php:8.1-apache
RUN docker-php-ext-install pdo pdo_mysql
# Для ожидания, пока база данных запустится
RUN apt-get update && apt-get install -y wait-for-it