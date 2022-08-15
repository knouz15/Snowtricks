# Snowtricks

[![SymfonyInsight](https://insight.symfony.com/projects/6a39a599-b148-46c8-9e81-d9e3de522820/big.svg)](https://insight.symfony.com/projects/6a39a599-b148-46c8-9e81-d9e3de522820)

# Initialise project

## Versions
* PHP 8.0
* Symfony 5.4.1
* Doctrine 2.10.4
* Postgresql 13

## Requirement
* PHP
* Symfony 
* Docker
* Composer
* yarn

## Steps

1. Clone the project repository

````
git clone https://github.com/geoffrey521/snowtricks.git
````

2. Download and install Composer dependencies

```
composer install
```

3. Download and install packages dependencies

````
yarn install
````

or

````
npm install
````

4. Build from asset

````
yarn encore dev
````

5. Using Database from docker

Make sure docker is running, run:

````
docker-compose up
````

6. Update database

````
symfony console d:m:m 
````

7. Load datas OR fixtures

````
symfony run psql < dump.sql      
````
or

````
symfony console d:f:l
````


8. Start server

````
symfony serve
````
9. Open mailer

````
symfony open:local:mailer
````


Local access:

````
symfony serve
````

Local access:

* Website project : 
  * Url: "localhost:8000"
* Mailer : 
  * Url: "localhost:48157"
* Adminer : 
  * Url "localhost:8080"
     *auth:
        * server: "database"
        * user: "symfony"
        * password: "ChangeMe"
        * database: "app"
