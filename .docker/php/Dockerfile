FROM php:7.2-fpm

#GIT (needed for python requirements)
RUN apt-get update && apt-get install -y git

#PYTHON
RUN apt-get update && apt-get install -y python python-pip

#SPHINX
COPY ./doc/requirements.txt /tmp/requirements.txt
RUN pip install --requirement /tmp/requirements.txt --user

#ZIP
RUN apt-get update \
    && apt-get install -y --no-install-recommends zip libzip-dev unzip

RUN docker-php-ext-configure zip
RUN docker-php-ext-install zip

#COMPOSER
RUN curl -sSk https://getcomposer.org/installer | php -- --disable-tls && \
	mv composer.phar /usr/local/bin/composer
ENV COMPOSER_MEMORY_LIMIT -1

#OPCACHE
RUN docker-php-ext-configure opcache --enable-opcache \
    && docker-php-ext-install opcache

#XDEBUG
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

#LOCALES
RUN apt-get update \
    && apt-get install -y --no-install-recommends locales \
    locales-all

ENV PATH "/root/.local/bin:${PATH}"

WORKDIR /home/wwwroot/