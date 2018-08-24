FROM php:7.2-fpm-alpine

RUN docker-php-ext-install sockets
RUN apk --update add \
	nodejs-current \
	nodejs-npm \
	git

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer 

WORKDIR /var/www
RUN rm -rf *
RUN git clone https://github.com/TauCetiStation/taucetistation.org.git .
#COPY . /var/www/

RUN composer install
RUN npm install
RUN npm install -g gulp
RUN gulp --prod
