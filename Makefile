.PHONY: up down restart logs bash install migrate keygen test start

start: up install migrate

up:
	docker compose up -d --build

down:
	docker compose down

restart:
	docker compose down && docker compose up -d --build

logs:
	docker compose logs -f

bash:
	docker compose exec api bash

install:
	docker compose exec api composer install --no-interaction

keygen:
	docker compose exec api php artisan key:generate

migrate:
	docker compose exec api php artisan migrate --force

test:
	docker compose exec api php artisan test
