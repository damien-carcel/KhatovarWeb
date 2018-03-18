up :
	docker-compose up -d

down :
	docker-compose down -v

composer :
	docker-compose exec fpm composer update --prefer-dist --optimize-autoloader

yarn :
	docker-compose run node yarn install

schema :
	docker-compose exec fpm bin/console doctrine:schema:update --force

fixtures :
	docker-compose exec fpm bin/console doctrine:fixtures:load --fixtures=features/Context/DataFixtures/ORM/LoadUserData.php

assets :
	docker-compose exec fpm bin/console assets:install www --symlink --relative

jsassets :
	docker-compose run node yarn run assets

initialize : composer yarn schema fixtures assets jsassets

phpcs :
	docker-compose exec fpm vendor/bin/phpcs -p --standard=PSR2 --extensions=php src

php-cs-fixer :
	docker-compose exec fpm vendor/bin/php-cs-fixer fix --dry-run -v --diff --config=.php_cs.php

phpspec :
	docker-compose exec fpm vendor/bin/phpspec run

behats :
	docker-compose exec fpm vendor/bin/behat

tests : phpcs php-cs-fixer phpspec behats
