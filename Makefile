define format
	bin/php-cs-fixer fix --config=.php_cs_fmt.dist $(1) || true
endef

define install_composer
	-[ ! -e bin/composer ] && wget https://getcomposer.org/composer.phar -O bin/composer && chmod +x bin/composer || true
endef

format:
	$(call format,)

setup-local:
	$(call install_composer)
	bin/composer install; \
	bin/git-hooks --uninstall 2> /dev/null; \
	bin/git-hooks --install bin;