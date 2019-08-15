#!/usr/bin/env bash

set -ex

composer self-update

if [[ $TRAVIS_PHP_VERSION  = '7.2' ]]; then PHPUNIT_FLAGS="--coverage-clover clover"; else PHPUNIT_FLAGS=""; fi
if [[ $TRAVIS_PHP_VERSION != '7.2' ]]; then phpenv config-rm xdebug.ini || true; fi
if [[ $TRAVIS_PHP_VERSION == '7.4'* ]]; then composer require --dev --no-update "symfony/cache:^4.4@dev"; fi

composer update $COMPOSER_FLAGS -n
