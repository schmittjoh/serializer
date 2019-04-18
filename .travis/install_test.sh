#!/usr/bin/env bash

set -ex

if [[ $TRAVIS_PHP_VERSION  = '7.2' ]]; then PHPUNIT_FLAGS="--coverage-clover clover"; else PHPUNIT_FLAGS=""; fi
if [[ $TRAVIS_PHP_VERSION != '7.2' ]]; then phpenv config-rm xdebug.ini; fi
composer self-update
composer update $COMPOSER_FLAGS -n
