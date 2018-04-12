FROM php:7.1-apache

WORKDIR /var/www/

RUN echo "PassEnv PDO_DSN_STRING\nPassEnv PDO_USERNAME\nPassEnv PDO_PASSWORD" > /etc/apache2/conf-enabled/expose-env.conf

RUN apt-get update && apt-get install -y mysql-client libmysqlclient-dev curl \
    libfreetype6-dev libjpeg62-turbo-dev libmcrypt-dev libpng12-dev \
    && docker-php-ext-install \
    iconv \
    mcrypt \
    gd \
    zip \
    mysqli \
    opcache \
    mbstring \
    pdo \
    pdo_mysql \
    gettext \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ 

# INSTALL XDEBUG
RUN pecl install xdebug 
RUN echo "zend_extension=xdebug.so\nxdebug.cli_color=1\nxdebug.remote_autostart=1\nxdebug.remote_connect_back=1" > /usr/local/etc/php/conf.d/xdebug.ini

# INSTALL COMPOSER 
RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer

# INSTALL PHPUNIT
RUN composer global require --dev "phpunit/phpunit"
ENV PATH /root/.composer/vendor/bin:$PATH
RUN ln -s /root/.composer/vendor/bin/phpunit /usr/bin/phpunit

# PHPUNIT/DBUNIT & MOCKERY
RUN composer require --dev "phpunit/dbunit" "mockery/mockery"
    
# GRAPHQL RELAY DATALOADER
RUN composer require "webonyx/graphql-php" "ivome/graphql-relay-php" "overblog/dataloader-php" "guzzlehttp/guzzle"

# Change DocumentRoot from /var/www/html to APACHE_DOCUMENT_ROOT
ENV APACHE_DOCUMENT_ROOT /var/www/server

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
RUN cd /var/www/ && rm -rf html