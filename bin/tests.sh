#!/bin/bash

echo "#############################################"
echo "                   TESTS                    "
echo "#############################################"

TEST_FAILED_MSG="The test failed, fix your code & try again =)"

docker-compose ps --services --filter "status=running" | grep "web"
if [[ $? -ne 0 ]]; then
    echo "Up web container to run the tests!"
    exit 1;
fi;


docker-compose ps --services --filter "status=running" | grep "php"
if [[ $? -ne 0 ]]; then
    echo "Up php container to run the tests!"
    exit 1;
fi;


docker-compose ps --services --filter "status=running" | grep "db"
if [[ $? -ne 0 ]]; then
    echo "Up db container to run the tests!"
    exit 1;
fi;

echo ""
echo "---------------------------------------------"
echo "                   PHPUnit"
echo "---------------------------------------------"

{ docker-compose exec php ./vendor/bin/phpunit --no-interaction || { echo -e "\u274c $TEST_FAILED_MSG" ; exit 1; } } \
&& echo "" && echo -e "\xE2\x9C\x94 PHPUnit passed"

echo ""
echo "---------------------------------------------"
echo "                   PHPCS"
echo "---------------------------------------------"
{ docker-compose exec php ./vendor/bin/phpcs  || { echo -e "\u274c $TEST_FAILED_MSG" ; exit 1; } } \
&& echo "" && echo -e "\xE2\x9C\x94 PHPCS passed"

echo ""
echo "---------------------------------------------"
echo "                   PHPStan"
echo "---------------------------------------------"
{ docker-compose exec php ./vendor/bin/phpstan analyse  --memory-limit=2G  || { echo -e "\u274c $TEST_FAILED_MSG" ; exit 1; } } \
&& echo "" && echo -e "\xE2\x9C\x94 PHPStan passed"

echo ""
echo "---------------------------------------------"
echo "                   PHPMD"
echo "---------------------------------------------"
{ docker-compose exec php ./vendor/bin/phpmd app text cleancode,codesize,controversial,design,naming,unusedcode  || { echo -e "\u274c $TEST_FAILED_MSG" ; exit 1; } } \
&& echo "" && echo -e "\xE2\x9C\x94 PHPMD passed"

echo ""
echo "---------------------------------------------"
echo -e "                   \xE2\x9C\x94 ALL TESTS PASSED"
echo "---------------------------------------------"

