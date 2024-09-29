FROM php:7.4.33

WORKDIR /app
VOLUME /app
EXPOSE 8000

RUN apt-get -y update \
&& apt-get install -y libicu-dev zlib1g-dev libpng-dev wget git unzip \
&& docker-php-ext-configure intl \
&& docker-php-ext-install intl pdo pdo_mysql gd

RUN wget https://getcomposer.org/download/2.3.10/composer.phar \
&& mv ./composer.phar /usr/bin/composer \
&& chmod +x /usr/bin/composer \
&& wget https://github.com/symfony-cli/symfony-cli/releases/download/v5.10.2/symfony-cli_5.10.2_amd64.deb \
&& dpkg -i symfony-cli_5.10.2_amd64.deb \
&& rm symfony-cli_5.10.2_amd64.deb


CMD ["symfony", "serve", "--no-tls", "--allow-http", "--port=8000"]


