#!/usr/bin/env bash

set -ex

composer self-update

if [[ $TRAVIS_PHP_VERSION != '7.2' ]]; then phpenv config-rm xdebug.ini || true; fi

composer update $COMPOSER_FLAGS -n
