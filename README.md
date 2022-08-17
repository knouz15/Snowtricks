[![SymfonyInsight](https://insight.symfony.com/projects/96e1976b-ac52-430f-a4aa-3e437679f26b/big.svg)](https://insight.symfony.com/projects/96e1976b-ac52-430f-a4aa-3e437679f26b)
https://insight.symfony.com/projects/96e1976b-ac52-430f-a4aa-3e437679f26b/analyses/63

# Initialise project

## Versions
* PHP 8.1.6
* Symfony 5.3.0
* Doctrine 2.7.1
* Mysql  10.4.24-MariaDB

## Requirement
* PHP
* Symfony 
* Docker
* Composer
* yarn

## Steps

1. Clone the project repository

````
git clone https://github.com/knouz15/Snowtricks.git
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


5. Using Database without docker

Update DATABASE_URL .env file with your database configuration:


DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/db_name


6. Create database

````
symfony console d:d:c 

````

Create database structure

````
symfony console m:m

````

7. Load datas fixtures

````
symfony console d:f:l
````

8. Start server

````
symfony serve

````

9. Update Mailer_DSN .env file with your configuration:


MAILER_DSN=smtp://Username:Password@Host:Port?encryption=tls&auth_mode=login
