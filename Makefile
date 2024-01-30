install:
	composer install
test:
	composer exec --verbose phpunit project/App/Tests/
test-coverage:
	composer exec --verbose phpunit project/App/Tests/ -- --coverage-clover build/logs/clover.xml