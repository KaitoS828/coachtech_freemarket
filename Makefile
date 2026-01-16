setup:
	@make build
	@make up
	@make copy-env
	@make install
	@make generating-key

init:
	docker-compose exec php php artisan migrate:fresh --seed

fresh:
	docker-compose exec php php artisan migrate:fresh --seed

up:
	docker-compose up -d

down:
	docker-compose down

item-test:
	docker-compose exec php ./vendor/bin/phpunit tests/Feature/ItemTest.php

test:
	docker-compose exec php ./vendor/bin/phpunit

cache:
	docker-compose exec php php artisan cache:clear
	docker-compose exec php php artisan config:clear
	docker-compose exec php php artisan route:clear
	docker-compose exec php php artisan view:clear

remake:
	@make fresh
	@make cache

build:
	docker-compose build --no-cache --force-rm

copy-env:
	cp src/.env.example src/.env

install:
	docker-compose exec php composer install

generating-key:
	docker-compose exec php php artisan key:generate

migration:
	docker-compose exec php php artisan make:migration $(name) $(filter-out $@,$(MAKECMDGOALS))
