ENV_FILE ?= .env
-include $(ENV_FILE)

build-docker:
	docker build . -t $(PHP_DEV_IMAGE):$(REVISION)
	
install:
	mkdir dev-tools-reports
	composer install

test:
	vendor/bin/phpunit
