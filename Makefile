.PHONY: build up down restart composer-install artisan stripe-login bash

DOCKER_COMPOSE = docker compose

build:
	@$(DOCKER_COMPOSE) build && make composer-install

up:
	@$(DOCKER_COMPOSE) up -d

down:
	@$(DOCKER_COMPOSE) down

restart:
	make down && make up

composer-install:
	@$(DOCKER_COMPOSE) run --rm app composer install

artisan:
	@$(DOCKER_COMPOSE) run --rm app php artisan

stripe-login:
	@$(DOCKER_COMPOSE) run --rm app stripe login

bash:
	@$(DOCKER_COMPOSE) exec app /bin/bash
