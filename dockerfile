# Use the official PHP 8.2 image with Apache
FROM php:8.2-apache

# Install Git, SQLite, and MySQLi
RUN apt-get update && \
    apt-get install -y git sqlite3 libsqlite3-dev default-mysql-client && \
    docker-php-ext-install pdo pdo_sqlite mysqli

# Clone your GitHub repository
RUN git clone https://github.com/almofada1/pw_final /var/www/html/

# Expose ports 80 and 8080
EXPOSE 80

# Set the working directory
WORKDIR /var/www/html/

# Define a volume for persistent storage
VOLUME /var/www/html/data
