[![Build Status](https://travis-ci.org/VSVverkeerskunde/gvq-api.svg?branch=master)](https://travis-ci.org/VSVverkeerskunde/gvq-api)
[![Coverage Status](https://coveralls.io/repos/github/VSVverkeerskunde/gvq-api/badge.svg?branch=master)](https://coveralls.io/github/VSVverkeerskunde/gvq-api?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/VSVverkeerskunde/gvq-api/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/VSVverkeerskunde/gvq-api/?branch=master)
[![Maintainability](https://api.codeclimate.com/v1/badges/99d90b15aff8a53e5418/maintainability)](https://codeclimate.com/github/VSVverkeerskunde/gvq-api/maintainability)

## Seeding
### Initial setup
Create a new database with settings taken from `.env` file:

`$ ./bin/console doctrine:database:create`

Create the tables inside this database with:

`$ ./bin/console doctrine:schema:create`
### Seeding categories
Adding all fixed categories can be done with the following custom command:

`$ ./bin/console gvq:seed-categories`

### Seeding users
Rename the included `fixed_users.yaml.dist` file to `fixed_users.yaml` or create your own data file. Adding all fixed users can be done with the following custom command:

`$ ./bin/console gvq:seed-users`

## Docker
### Basics
Edit the your host file and add the following:
```
127.0.0.1	gvq-api.test
127.0.0.1	mailhog.gvq-api.test
127.0.0.1	mysql.gvq-api.test
```

Install sources by running:
```
$ composer install
```

Start docker by running:
```
$ docker-compose up -d
```

Connect to api on: http://gvq-api.test:8000/

Connect to mailhog on: http://mailhog.gvq-api.test:8025/

Connect to MySQL on: mysql.gvq-api.test:33066

### Database
Create the schema with (make sure to have correct `DATABASE_URL` string inside `.env`):
```
$ docker-compose exec web bash -c "./bin/console doctrine:schema:create"
```
Example of `DATABASE_URL` with values inside `.env`:
```
DATABASE_URL=mysql://$DB_USER:$DB_PASSWORD@$DB_HOST:3306/$DB_NAME
```
Seeding categories
```
$ docker-compose exec web bash -c "./bin/console gvq:seed-categories"
```

Seeding users (make sure to provide an input file)
```
$ docker-compose exec web bash -c "./bin/console gvq:seed-users"
```
