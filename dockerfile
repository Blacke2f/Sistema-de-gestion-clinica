FROM php:8.3-fpm-alpine

WORKDIR /app

# Install system dependencies and PHP extensions
RUN apk add --no-cache \
    curl \
    git \
    unzip \
    mysql-client \
    $PHPIZE_DEPS && \
    docker-php-ext-install \
    pdo \
    pdo_mysql \
    bcmath \
    ctype \
    fileinfo \
    json \
    mbstring \
    tokenizer \
    xml && \
    apk del $PHPIZE_DEPS

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy configuration files
COPY docker/php.ini /usr/local/etc/php/conf.d/docker.ini
COPY docker/www.conf /usr/local/etc/php-fpm.d/www.conf

# Copy application
COPY . .

# Install Laravel dependencies
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader || true

# Set permissions
RUN mkdir -p storage/logs storage/framework/views storage/framework/cache storage/framework/sessions && \
    chown -R www-data:www-data /app && \
    chmod -R 755 storage bootstrap/cache

EXPOSE 9000

CMD ["php-fpm"]
