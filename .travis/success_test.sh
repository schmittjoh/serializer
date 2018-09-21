#!/usr/bin/env bash

if [[ $TRAVIS_PHP_VERSION = '7.2' ]]; then wget https://scrutinizer-ci.com/ocular.phar; fi
if [[ $TRAVIS_PHP_VERSION = '7.2' ]]; then php ocular.phar code-coverage:upload --format=php-clover clover; fi
