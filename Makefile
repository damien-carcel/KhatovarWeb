MYSQL_OUTPUT_PORT ?= 0
PHP_OUTPUT_PORT ?= 0

# Build Docker images

.PHONY: pull
pull:
	docker-compose pull --ignore-pull-failures

.PHONY: build-fpm
build-fpm: pull
	DOCKER_BUILDKIT=1 docker build --pull . --tag carcel/khatovar/dev:fpm --target fpm

.PHONY: build-node
build-node: pull
	DOCKER_BUILDKIT=1 docker build --pull . --tag carcel/khatovar/dev:node --target node

.PHONY: build
build: build-fpm build-node

# Prepare the application dependencies

.PHONY: install-back-dependencies
install-back-dependencies:  build-fpm
	docker-compose run --rm php composer install --prefer-dist --optimize-autoloader --no-interaction --no-scripts

.PHONY: install-front-dependencies
install-front-dependencies: build-node
	docker-compose run --rm node yarn install

.PHONY: install-dependencies
install-dependencies: install-back-dependencies install-front-dependencies

.PHONY: update-back-dependencies
update-back-dependencies: build-fpm
	docker-compose run --rm php composer update --prefer-dist --optimize-autoloader --no-interaction --no-scripts

.PHONY: update-front-dependencies
update-front-dependencies: build-node
	docker-compose run --rm node yarn upgrade-interactive --latest

.PHONY: update-dependencies
update-dependencies: update-back-dependencies update-front-dependencies

# Serve the application

.PHONY: mysql	# It should depends on "install-back-dependencies" because it uses PHP dev image,
mysql:			# but as a result this image is built twice, which is an issue without layer caching.
	docker-compose up -d mysql
	sh $(CURDIR)/docker/mysql/wait_for_it.sh
	docker-compose run --rm php bin/console doctrine:schema:update --force

.PHONY: fixtures
fixtures:
	docker-compose run --rm php bin/console doctrine:fixtures:load --fixtures=tests/fixtures/ORM/LoadUserData.php -n

.PHONY: assets
assets:
	docker-compose run --rm php bin/console --env=prod assets:install www --symlink --relative
	docker-compose run --rm node yarn run assets

.PHONY: server-run
server-run: install-dependencies assets mysql fixtures
	docker-compose run --rm --service-ports php bin/console server:run -d www 0.0.0.0:8000

.PHONY: install
install: install-dependencies assets mysql fixtures
	docker-compose up -d nginx

# Clean the containers

.PHONY: down
down:
	docker-compose down -v
