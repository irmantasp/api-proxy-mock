include .env

.PHONY: up down stop prune ps shell bash logs mutagen

default: up

## help	:	Print commands help.
help : docker.mk
	@sed -n 's/^##//p' $<

## up	:	Start up containers.
up:
	@echo "Starting up containers for for $(PROJECT_NAME)..."
#	docker-compose pull
	docker-compose up -d --remove-orphans

buildup:
	docker-compose pull
	docker-compose up --build -d --remove-orphans $(filter-out $@,$(MAKECMDGOALS))

## down	:	Stop containers.
down: stop

## start	:	Start containers without updating.
start:
	@echo "Starting containers for $(PROJECT_NAME) from where you left off..."
	@docker-compose start

## stop	:	Stop containers.
stop:
	@echo "Stopping containers for $(PROJECT_NAME)..."
	@docker-compose stop

## prune	:	Remove containers and their volumes.
##		You can optionally pass an argument with the service name to prune single container
##		prune mariadb	: Prune `mariadb` container and remove its volumes.
##		prune mariadb solr	: Prune `mariadb` and `solr` containers and remove their volumes.
prune:
	@echo "Removing containers for $(PROJECT_NAME)..."
	@docker-compose down -v $(filter-out $@,$(MAKECMDGOALS))

## ps	:	List running containers.
ps:
	@docker ps --filter name='$(PROJECT_NAME)*'

## shell	:	Access `php` container via shell.
##		You can optionally pass an argument with a service name to open a shell on the specified container
shell:
	docker exec -ti -e COLUMNS=$(shell tput cols) -e LINES=$(shell tput lines) $(shell docker ps --filter name='$(PROJECT_NAME)_$(or $(filter-out $@,$(MAKECMDGOALS)), 'php')' --format "{{ .ID }}") sh

## bash	:	Access `php` container via bash shell.
##		You can optionally pass an argument with a service name to open a shell on the specified container
bash:
	docker exec -ti -e COLUMNS=$(shell tput cols) -e LINES=$(shell tput lines) $(shell docker ps --filter name='$(PROJECT_NAME)_$(or $(filter-out $@,$(MAKECMDGOALS)), 'php')' --format "{{ .ID }}") bash

## composer	:	Access `php` container via bash shell.
composer:
	docker exec -ti -e COLUMNS=$(shell tput cols) -e LINES=$(shell tput lines) $(shell docker ps --filter name='$(PROJECT_NAME)_php' --format "{{ .ID }}") composer $(filter-out $@,$(MAKECMDGOALS))

## logs	:	View containers logs.
##		You can optionally pass an argument with the service name to limit logs
##		logs php	: View `php` container logs.
##		logs nginx php	: View `nginx` and `php` containers logs.
logs:
	@docker-compose logs -f $(filter-out $@,$(MAKECMDGOALS))

server:
	@symfony serve --dir public

build:
	@docker build --no-cache -t irmpdz/api-proxy-mock:$(filter-out $@,$(MAKECMDGOALS)) -t irmpdz/api-proxy-mock:latest .

push:
	@docker push irmpdz/api-proxy-mock:$(filter-out $@,$(MAKECMDGOALS))
	@docker push irmpdz/api-proxy-mock:latest

# https://stackoverflow.com/a/6273809/1826109
%:
	@: