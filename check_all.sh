./check_strict_types.sh
./vendor/bin/phpstan --level=7 analyse src tests
./vendor/bin/phpcs
./vendor/bin/phpunit --no-coverage
./bin/console lint:twig templates/
