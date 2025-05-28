bash:
	docker exec -it "weather_app_php" bash

build:
	sudo docker compose build
	sudo docker compose up -d --remove-orphans

run:
	sudo docker-compose up -d

stop:
	docker-compose down

cache:
	docker exec -it "weather_app_php" sh -c 'php /var/www/html/default/bin/console cache:clear'
	docker-compose down

.DEFAULT_GOAL := all