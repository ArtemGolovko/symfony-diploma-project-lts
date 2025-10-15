FROM php:7.4.33-fpm

WORKDIR /var/www/html

RUN apt-get -y update \
&& apt-get install -y libicu-dev zlib1g-dev libpng-dev wget git unzip default-mysql-client \
&& docker-php-ext-configure intl \
&& docker-php-ext-install intl pdo pdo_mysql gd \
&& apt-get -y autoremove \
&& apt-get clean \
&& wget https://github.com/symfony-cli/symfony-cli/releases/download/v5.10.2/symfony-cli_5.10.2_amd64.deb \
&& dpkg -i symfony-cli_5.10.2_amd64.deb \
&& rm symfony-cli_5.10.2_amd64.deb

# COPY ./.docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini

COPY --from=composer/composer:2.3.10-bin /composer /usr/bin/composer

CMD ["php-fpm"]

# Document that we use port 9000
EXPOSE 9000
