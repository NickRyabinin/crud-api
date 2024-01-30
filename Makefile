test:
	PHPUnit/phpunit.phar project/App/Tests/
lint:
	phpcs -- --standard=PSR12 .
test-coverage:
	PHPUnit/phpunit.phar project/App/Tests/ -- --coverage-clover build/logs/clover.xml