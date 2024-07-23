.PHONY: build up down restart composer-install artisan stripe-login bash

build:
	docker-compose build

up:
	docker-compose up -d

down:
	docker-compose down

restart:
	docker-compose down && docker-compose up -d

composer-install:
	docker-compose run --rm app composer install

artisan:
	docker-compose run --rm app php artisan

stripe-login:
	docker-compose run --rm app stripe login

bash:
	docker-compose exec app /bin/bash
