build:
	docker build . -t free-elephants/php-di-dev
	./bin/composer install

test:
	docker run --rm -v $(PWD):/var/www free-elephants/php-di-dev vendor/bin/phpunit
