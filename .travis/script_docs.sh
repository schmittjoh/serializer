#!/usr/bin/env bash

set -ex

cd doc && sphinx-build -W -b html -d _build/doctrees . _build/html
