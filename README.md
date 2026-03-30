# Airport API

This codebase is an implementation of an API that can supply geospatial airport data to an iOS application that helps users map and geographically compare airports around the world.

It is built using the Slim PHP framework and it runs in an NGINX/PHP-FPM/MySQL-based Docker environment. There is a Swagger documentation page that can be used to send requests and receive responses in your browser for all of the API endpoints that exist in the app. 

## Stack Info
- NGINX (latest version)
- PHP-FPM 8.3
- MySQL 8.0
- Slim PHP 4, based on the Slim Framework 4 Skeleton Application (`slimphp/slim-skeleton`)

## Installation and Setup

The only pre-requisite for setup is that you have Docker installed on your host machine. 

#### Steps for setup:

##### 1) Clone this repository
``` git clone git@github.com:lonecactus/airport_api.git```

##### 2) Start the Docker containers
From the command line, enter the root folder of the repo and start up the Docker containers:

```docker compose up -d --build```

##### 3) Shell into the PHP-FPM container
Once the containers are running, enter the PHP-FPM container. You can use `docker ps` to identify the name of the container that is running PHP-FPM; it will be something like `airport_api-php-fpm-1`. Assuming that name, you can enter the container with:

```docker exec -it airport_api-php-fpm-1 /bin/sh```

##### 4) Install composer dependencies
Once you are inside the PHP-FPM container, install the composer dependencies for this project: 

```composer install```

##### 5) Create and populate database

Apply the database migrations to create and populate the database with the airport data: 

```vendor/bin/phinx migrate && vendor/bin/phinx seed:run```

##### 6) View Swagger documentation
If everything has gone correctly, you should now be able to visit the Swagger API documentation  `http://localhost:8080/docs` and try out all of the various endpoints in your browser
