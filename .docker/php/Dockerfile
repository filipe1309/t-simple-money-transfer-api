FROM php:8.0.2-fpm

# Install system dependencies
RUN apt-get update && apt-get upgrade -y
RUN apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    iproute2

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd tokenizer mysqli
RUN pecl install redis xdebug-3.0.3
RUN docker-php-ext-enable redis xdebug

RUN echo "[Xdebug]\n" \
    "xdebug.mode=coverage\n" \
    "xdebug.start_with_request=yes\n" \
    "xdebug.discover_client_host=1\n" \
    "xdebug.idekey=DOCKER_XDEBUG\n" \
    "xdebug.max_nesting_level=1000\n" \
    "xdebug.log_level=0\n" \
    "xdebug.client_host=host.docker.internal" > /usr/local/etc/php/conf.d/xdebug.ini

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

USER $user

RUN chown -R www-data:www-data /var/www
RUN usermod -u 1000 www-data
USER www-data

# Set working directory
WORKDIR /var/www/html

COPY entrypoint.sh /usr/local/bin/

ENTRYPOINT ["entrypoint.sh"]
