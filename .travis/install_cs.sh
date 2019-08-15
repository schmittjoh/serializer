#!/usr/bin/env bash

set -ex

composer self-update

phpenv config-rm xdebug.ini

composer update -n
