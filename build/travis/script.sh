#! /bin/bash

set -x

cd ../wiki/tests/phpunit
php phpunit.php -c ../../extensions/WikibaseElastic/phpunit.xml.dist
