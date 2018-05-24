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
