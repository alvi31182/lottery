DOCKER_COMPOSE = docker-compose
DOCKER = docker

.PHONY: help

RESET = \033[0m
YELLOW = \033[0;33m

help:
	@echo "$(YELLOW)Usage: make [target]$(RESET)"
	@echo "$(YELLOW)Targets:$(RESET)"
	@echo "$(YELLOW)  build$(RESET)               Build Docker images"
	@echo "$(YELLOW)  up$(RESET)                  Start Docker containers"
	@echo "$(YELLOW)  down$(RESET)                Stop and remove Docker containers"
	@echo "$(YELLOW)  php$(RESET)                 Access PHP container"
	@echo "$(YELLOW)  ip-kafka$(RESET)            Display Kafka container IP address"
	@echo "$(YELLOW)  copy-config-files$(RESET)   Copy config files from .dist files"

build: copy-config-files
	@echo "Building Docker images..."
	PWD=$(pwd) $(DOCKER_COMPOSE) up --build
down:
	PWD=$(pwd) $(DOCKER_COMPOSE) -f docker-compose.yml -f docker-compose.php_cli.yml -f docker-compose.kafka.yml down --remove-orphans

up:
	PWD=$(pwd) $(DOCKER_COMPOSE) -f docker-compose.yml -f docker-compose.php_cli.yml -f docker-compose.kafka.yml up  -d

php:
	docker exec -it lottery_php bash

ip-kafka:
	docker inspect -f '{{range.NetworkSettings.Networks}}{{.IPAddress}}{{end}}' lottery_kafka

copy-config-files:
	cp docker-compose.yml.dist docker-compose.yml
	cp docker-compose.php_cli.yml.dist docker-compose.php_cli.yml
	cp docker-compose.kafka.yml.dist docker-compose.kafka.yml