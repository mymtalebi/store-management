define format
	bin/php-cs-fixer fix --config=.php_cs_fmt.dist $(1) || true
endef

format:
	$(call format,)

