#!/usr/bin/env bash

set -ex

vendor/bin/phpunit $PHPUNIT_FLAGS
phpenv config-rm xdebug.ini || true
php tests/benchmark.php json 3
php tests/benchmark.php xml 3

if [[ $TRAVIS_PHP_VERSION != '7.4'* ]]; then vendor/bin/phpcs; fi

