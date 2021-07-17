#!/bin/bash

echo "#############################################"
echo "                   TESTS                    "
echo "#############################################"

echo ""
echo "---------------------------------------------"
echo "                   PHPUnit"
echo "---------------------------------------------"
docker-compose exec php ./vendor/bin/phpunit --do-not-cache-result && echo "" && echo "PHPStan passed"

echo ""
echo "---------------------------------------------"
echo "                   PHPCS"
echo "---------------------------------------------"
docker-compose exec php ./vendor/bin/phpcs && echo "" && echo "PHPStan passed"

echo ""
echo "---------------------------------------------"
echo "                   PHPStan"
echo "---------------------------------------------"
docker-compose exec php ./vendor/bin/phpstan analyse  --memory-limit=2G && echo "" && echo "PHPStan passed"

echo ""
echo "---------------------------------------------"
echo "                   PHPMD"
echo "---------------------------------------------"
docker-compose exec php ./vendor/bin/phpmd app text cleancode,codesize,controversial,design,naming,unusedcode && echo "" && echo "PHPMD passed"

echo ""
echo "---------------------------------------------"
echo "                   ALL TESTS PASSED"
echo "---------------------------------------------"

