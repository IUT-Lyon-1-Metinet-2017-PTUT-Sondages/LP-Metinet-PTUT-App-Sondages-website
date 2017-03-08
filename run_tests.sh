#!/usr/bin/env bash

set -e

php bin/console doctrine:database:create --if-not-exists --env test
./vendor/bin/phpunit
php bin/console doctrine:database:drop --if-exists --force --env test
