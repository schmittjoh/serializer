#!/usr/bin/env bash

set -ex

composer self-update

if [[ $TRAVIS_PHP_VERSION != '7.2' ]]; then phpenv config-rm xdebug.ini || true; fi

if [[ $VERY_LATEST == '1' ]]; then composer remove --dev doctrine/phpcr-odm --no-update -n; fi

composer update $COMPOSER_FLAGS -n
