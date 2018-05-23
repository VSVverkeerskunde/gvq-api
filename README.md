[![Build Status](https://travis-ci.org/VSVverkeerskunde/gvq-api.svg?branch=master)](https://travis-ci.org/VSVverkeerskunde/gvq-api)
[![Coverage Status](https://coveralls.io/repos/github/VSVverkeerskunde/gvq-api/badge.svg?branch=master)](https://coveralls.io/github/VSVverkeerskunde/gvq-api?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/VSVverkeerskunde/gvq-api/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/VSVverkeerskunde/gvq-api/?branch=master)
[![Maintainability](https://api.codeclimate.com/v1/badges/99d90b15aff8a53e5418/maintainability)](https://codeclimate.com/github/VSVverkeerskunde/gvq-api/maintainability)

## Docker
Edit the your host file and add the following:
```
127.0.0.1	gvq-api.test
127.0.0.1	mailhog.gvq-api.test
127.0.0.1	mysql.gvq-api.test
```

Start docker by running:
```
$ docker-compose up -d
```

Connect to api on: http://gvq-api.test:8000/

Connect to mailhog on: http://mailhog.gvq-api.test:8025/

Connect to MySQL on: mysql.gvq-api.test:33066

