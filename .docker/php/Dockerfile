FROM php:7.4.33-fpm

WORKDIR /var/www/html

ARG GID

RUN apt-get -y update \
&& apt-get install -y gosu

RUN apt-get -y update \
&& apt-get install -y libicu-dev zlib1g-dev libpng-dev wget git unzip default-mysql-client \
&& docker-php-ext-configure intl \
&& docker-php-ext-install intl pdo pdo_mysql gd \
&& apt-get -y autoremove \
&& apt-get clean \
&& wget https://github.com/symfony-cli/symfony-cli/releases/download/v5.10.2/symfony-cli_5.10.2_amd64.deb \
&& dpkg -i symfony-cli_5.10.2_amd64.deb \
&& rm symfony-cli_5.10.2_amd64.deb

RUN groupadd --force -g $GID dev
RUN useradd -ms /bin/bash --no-user-group -g $GID -u 1337 dev

COPY --from=composer/composer:2.3.10-bin /composer /usr/bin/composer
COPY --link --chmod=755 ./.docker/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint

ENTRYPOINT ["docker-entrypoint"]
CMD ["php-fpm"]

# Document that we use port 9000
EXPOSE 9000
