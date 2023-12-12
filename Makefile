DOCKER_COMPOSE = docker-compose
DOCKER = docker

build:
	@echo "Building Docker images..."
	PWD=$(pwd) $(DOCKER_COMPOSE) up --build
down:
	PWD=$(pwd) $(DOCKER_COMPOSE) -f docker-compose.yml down --remove-orphans

up:
	PWD=$(pwd) $(DOCKER_COMPOSE) -f docker-compose.yml up  -d

php:
	docker exec -it lottery_php bash