FROM php:8.3-fpm-alpine

ENV NPM_CONFIG_PREFIX=/home/node/.npm-global
ENV PATH=$PATH:/home/node/.npm-global/bin

RUN apk add --no-cache linux-headers
RUN docker-php-ext-install sockets
RUN apk --update add \
	nodejs \
	npm \
	git

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer 

WORKDIR /var/www/taucetistation-org/
RUN rm -rf *

RUN chown www-data:www-data .
USER www-data
RUN git clone https://github.com/TauCetiStation/taucetistation.org.git .

RUN composer install

RUN npm install
RUN npm run css
RUN npm run js
