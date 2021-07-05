help:
	@echo "This makefile is intended for development purpose."
	@echo ""
	@echo "Targets:"
	@echo "    install:               This will setup the development environment"
	@echo "    up:                    docker-compose up the development environment"
	@echo "    down:                  docker-compose down the development environment"
	@echo "    build:                 rebuild all the needed container"
	@echo "    test:                  will run all automation test"
	@echo "    php:                   will connect to the php container"
	@echo "    cs-check:              will run phpcs on the code, should be run before pull request"
	@echo "    cs-fix:                will try to fix any coding style error"
	@echo "    doc-generate:          will generate the doc using sphinx"

.ONESHELL:

install: build provision test

up:
	docker-compose up -d

down:
	docker-compose down --remove-orphans

build:
	docker-compose build --pull

php:
	docker exec -it serializer_php bash

test: up
	docker-compose exec php php vendor/bin/phpunit

provision: up
	docker-compose exec php composer install

cs-check:
	docker-compose exec php php vendor/bin/phpcs

cs-fix:
	docker-compose exec php php vendor/bin/phpcbf

doc-generate:
	docker-compose exec php bash -c "cd doc && sphinx-build -W -b html -d _build/doctrees . _build/html"

