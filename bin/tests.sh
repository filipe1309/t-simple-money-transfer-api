#!/bin/bash

echo "#############################################"
echo "                   TESTS                    "
echo "#############################################"

TEST_FAILED_MSG="The test failed, fix your code & try again =)"

echo ""
echo "---------------------------------------------"
echo "                   PHPUnit"
echo "---------------------------------------------"
# docker-compose exec php
{ ./vendor/bin/phpunit --no-interaction || { echo -e "\u274c $TEST_FAILED_MSG" ; exit 1; } } \
&& echo "" && echo -e "\xE2\x9C\x94 PHPUnit passed"

echo ""
echo "---------------------------------------------"
echo "                   PHPCS"
echo "---------------------------------------------"
{ ./vendor/bin/phpcs  || { echo -e "\u274c $TEST_FAILED_MSG" ; exit 1; } } \
&& echo "" && echo -e "\xE2\x9C\x94 PHPCS passed"

echo ""
echo "---------------------------------------------"
echo "                   PHPStan"
echo "---------------------------------------------"
{ ./vendor/bin/phpstan analyse  --memory-limit=2G  || { echo -e "\u274c $TEST_FAILED_MSG" ; exit 1; } } \
&& echo "" && echo -e "\xE2\x9C\x94 PHPStan passed"

echo ""
echo "---------------------------------------------"
echo "                   PHPMD"
echo "---------------------------------------------"
{ ./vendor/bin/phpmd app text cleancode,codesize,controversial,design,naming,unusedcode  || { echo -e "\u274c $TEST_FAILED_MSG" ; exit 1; } } \
&& echo "" && echo -e "\xE2\x9C\x94 PHPMD passed"

echo ""
echo "---------------------------------------------"
echo -e "                   \xE2\x9C\x94 ALL TESTS PASSED"
echo "---------------------------------------------"

