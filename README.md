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

With the optional file argument it is possible to specify an arbitrary file with categories.

### Seeding users
Rename the included `fixed_users.yaml.dist` file to `fixed_users.yaml` or create your own data file. Adding all fixed users can be done with the following custom command:

`$ ./bin/console gvq:seed-users`

With the optional file argument it is possible to specify an arbitrary file with users.

## Docker
### Basics
Edit the your host file and add the following:
```
127.0.0.1	gvq-api.test
127.0.0.1	mailhog.gvq-api.test
127.0.0.1	mysql.gvq-api.test
127.0.0.1	redis.gvq-api.test
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
Seeding categories (by default taken from categories.yaml)
```
$ docker-compose exec web bash -c "./bin/console gvq:seed-categories"
```

Seeding users (make sure to provide an input file)
```
$ docker-compose exec web bash -c "./bin/console gvq:seed-users"
```

### Image upload
If you want to use s3 as upload location, then change inside `env`:
```
UPLOAD_TARGET=remote
UPLOAD_PATH=*url_to_s3_bucket*
``` 
Example:
```
UPLOAD_TARGET=remote
UPLOAD_PATH=https://s3-eu-west-1.amazonaws.com/verkeersquiz-test/
```
and fill in the necessary credential and bucket details.

To use the local filesystem, leave the default
values unchanged:
```
UPLOAD_TARGET=local
UPLOAD_PATH=/uploads/
```
