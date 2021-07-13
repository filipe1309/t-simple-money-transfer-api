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
