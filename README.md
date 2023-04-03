## Requirements
- Docker : https://www.docker.com/get-started
- Docker-compose: https://docs.docker.com/compose/install/

## Installation
- git clone https://github.com/maryem30/article.git
- cd article
- docker-compose build
- docker-compose up -d
- docker exec -it -u dev sf4_php bash
- cd sf4
- composer install
- php bin/console doctrine:migrations:migrate

## Show Project 
- Access to http://localhost
- Access to database with http://localhost:8080
