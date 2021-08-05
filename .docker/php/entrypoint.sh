#!/bin/bash

composer install --no-interaction --ignore-platform-reqs --optimize-autoloader
# --no-dev for prod

php artisan migrate

php-fpm
