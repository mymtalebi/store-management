FROM php:7.2-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends git zip unzip wget \
    && rm -rf /var/lib/apt/lists/*

# lumen packages
RUN docker-php-ext-install tokenizer mysqli pdo_mysql

# download composer
RUN curl --silent --show-error https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# download wait-for-it
RUN wget https://raw.githubusercontent.com/vishnubob/wait-for-it/master/wait-for-it.sh -O /root/wait-for-it.sh \
    && chmod u+x /root/wait-for-it.sh

RUN a2enmod rewrite

ENV APACHE_DOCUMENT_ROOT /var/www/html/public

COPY .docker/default.conf /etc/apache2/sites-available/000-default.conf