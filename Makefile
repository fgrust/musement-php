include .env

help:
		@echo ""
		@echo "usage: make COMMAND"
		@echo ""
		@echo "Commands:"
		@echo "	clean               Clean directories for reset"
		@echo "	composer-update     Update PHP dependencies with composer"
		@echo "	docker-start        Create and start containers"
		@echo "	docker-stop         Stop and clear all services"
		@echo "	migrate             Migrate database"
		@echo "	migrate-rollback    Rollback the last migration"
		@echo "	test                Test application"
		@echo "	service             Run the service to fetch Musement information"

clean:
		@rm -Rf vendor
		@rm -Rf composer.lock
		@rm -Rf .phpunit.result.cache

composer-update: 
		@docker-compose exec php composer update

docker-start:
		@docker-compose up -d

docker-stop:
		@docker-compose down -v
		@make clean

migrate: composer-update
		@docker-compose exec php composer migrate

migrate-rollback: composer-update
		@docker-compose exec php composer migrate-rollback

test:
		@docker-compose exec php composer test

test-unit:
		@docker-compose exec php composer test:unit

test-types:
		@docker-compose exec php composer test:types

service:
		@docker-compose exec php composer fetch
