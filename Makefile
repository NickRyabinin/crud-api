install:
	composer install
lint:
	composer exec --verbose phpcs -- --standard=PSR12 project
test:
	composer exec --verbose phpunit project/App/Tests/
test-coverage:
	composer exec --verbose phpunit project/App/Tests/ -- --coverage-clover build/logs/clover.xml