FROM php:8.1-apache

# Habilitar el módulo rewrite de Apache (común en aplicaciones PHP)
RUN a2enmod rewrite

# Instalar extensiones PHP comunes (ejemplo: MySQL)
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Copiar el código de tu aplicación al contenedor
COPY . /var/www/html/

# Asegurar los permisos correctos para Apache
RUN chown -R www-data:www-data /var/www/html
