FROM php:fpm-alpine

RUN docker-php-ext-install sockets
RUN apk --update add \
	nodejs-current \
	nodejs-npm \
	git

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer 

#https://github.com/npm/npm/issues/20861
RUN npm config set unsafe-perm true
RUN npm install -g gulp

WORKDIR /var/www/taucetistation-org/
RUN rm -rf *
RUN chown www-data:www-data .
USER www-data
RUN git clone https://github.com/TauCetiStation/taucetistation.org.git .

RUN composer install

RUN npm install
RUN gulp --prod
