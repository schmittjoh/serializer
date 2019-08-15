#!/usr/bin/env bash

set -ex

vendor/bin/phpunit $PHPUNIT_FLAGS
phpenv config-rm xdebug.ini || true
php tests/benchmark.php json 3
php tests/benchmark.php xml 3

