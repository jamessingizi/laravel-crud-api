CURRENT_DIRECTORY := $(shell pwd)

.PHONY: up stop down restart build tail seed test coverage analyse lint fix db

up:
	@docker-compose -f docker-compose.yml -f docker-compose.local.yml up -d

stop:
	@docker-compose stop

down:
	@docker-compose down

restart: stop up

build:
	@docker-compose -f docker-compose.yml -f docker-compose.local.yml up -d --build

tail:
	@docker-compose logs -f app

seed:
	@docker-compose exec app php artisan db:seed

test: up
	@docker-compose exec app php artisan test

coverage: up
	@docker-compose exec -e XDEBUG_MODE=coverage app php artisan test --coverage

analyse: up
	@docker-compose exec app ./vendor/bin/phpstan analyse

lint:
	@docker-compose exec app ./vendor/bin/duster lint

fix:
	@docker-compose exec app ./vendor/bin/duster fix

db:
	@docker-compose exec mysql mysql -u root -p
