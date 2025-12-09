fix:
	./vendor/bin/php-cs-fixer --allow-risky=yes fix

# Output to `coverage-html/`
code-coverage-local:
	XDEBUG_MODE=coverage vendor/bin/phpunit --coverage-html coverage-html
