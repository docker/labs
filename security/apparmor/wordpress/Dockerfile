FROM php:5.6-apache

RUN apt-get update && apt-get install -y libpng12-dev libjpeg-dev && rm -rf /var/lib/apt/lists/* \
	&& docker-php-ext-configure gd --with-png-dir=/usr --with-jpeg-dir=/usr \
	&& docker-php-ext-install gd mysqli opcache 

COPY ./html /var/www/html
COPY ./zues /var/www/html/wp-content/themes/zues
COPY ./php.ini /usr/local/etc/php/

RUN mkdir /var/www/html/wp-content/uploads

RUN chown -R www-data:www-data /var/www/html

