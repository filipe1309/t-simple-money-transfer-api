#!/bin/bash

echo "#############################################"
echo "                   TESTS                    "
echo "#############################################"

echo ""
echo "---------------------------------------------"
echo "                   PHPUnit"
echo "---------------------------------------------"
docker-compose exec php ./vendor/bin/phpunit --do-not-cache-result

echo ""
echo "---------------------------------------------"
echo "                   PHPCS"
echo "---------------------------------------------"
docker-compose exec php ./vendor/bin/phpcs

echo ""
echo "---------------------------------------------"
echo "                   PHPStan"
echo "---------------------------------------------"
docker-compose exec php ./vendor/bin/phpstan analyse  --memory-limit=2G
