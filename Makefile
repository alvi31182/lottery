DOCKER_COMPOSE = docker-compose
DOCKER = docker

build:
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