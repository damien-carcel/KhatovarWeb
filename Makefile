up :
	docker-compose up -d

restart :
	docker-compose stop fpm && docker-compose rm -f fpm && docker-compose up -d

down :
	docker-compose down -v

composer :
	docker-compose exec fpm composer install --prefer-dist --optimize-autoloader

yarn :
	docker-compose run --rm node yarn install

schema :
	docker-compose exec fpm bin/console doctrine:schema:update --force

assets :
	docker-compose exec fpm bin/console assets:install www --symlink --relative

bower :
	docker-compose run node yarn run assets

initialize : composer yarn schema assets bower

coupling :
	docker-compose exec fpm vendor/bin/php-coupling-detector detect --config-file=.php_cd.php

phpcs :
	docker-compose exec fpm vendor/bin/phpcs -p --standard=PSR2 --extensions=php src tests/acceptance tests/integration tests/system

php-cs-fixer-dry-run :
	docker-compose exec fpm vendor/bin/php-cs-fixer fix --dry-run -v --diff --config=.php_cs.php

php-cs-fixer :
	docker-compose exec fpm vendor/bin/php-cs-fixer fix -v --diff --config=.php_cs.php

phpspec :
	docker-compose exec fpm vendor/bin/phpspec run

integration :
	docker-compose exec fpm vendor/bin/behat --profile=integration

acceptance :
	docker-compose exec fpm vendor/bin/behat --profile=acceptance

system :
	docker-compose exec fpm vendor/bin/behat --profile=system

legacy :
	docker-compose exec fpm vendor/bin/behat --profile=legacy

tests : coupling phpcs php-cs-fixer-dry-run phpspec integration acceptance system legacy
