.PHONY: build up down restart composer-install artisan stripe-login bash

DOCKER_COMPOSE = docker compose

build:
	@$(DOCKER_COMPOSE) build

up:
	@$(DOCKER_COMPOSE) up -d

down:
	@$(DOCKER_COMPOSE) down

restart:
	@$(DOCKER_COMPOSE) down && @$(DOCKER_COMPOSE) up -d

composer-install:
	@$(DOCKER_COMPOSE) run --rm app composer install

artisan:
	@$(DOCKER_COMPOSE) run --rm app php artisan

stripe-login:
	@$(DOCKER_COMPOSE) run --rm app stripe login

bash:
	@$(DOCKER_COMPOSE) exec app /bin/bash
