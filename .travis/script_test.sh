#!/usr/bin/env bash

set -ex

vendor/bin/psalm.phar || true # As we are only now starting to run Psalm we don't expect it to succed right now.
vendor/bin/phpunit $PHPUNIT_FLAGS
phpenv config-rm xdebug.ini || true
php tests/benchmark.php json 3
php tests/benchmark.php xml 3
vendor/bin/phpcs
