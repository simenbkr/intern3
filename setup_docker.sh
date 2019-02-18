#!/bin/bash

mkdir src
mv * src
mv src/config_example.php src/config.php
sed -i 's/localhost/mariadb/g' src/config.php
sed -i 's/intern3_dev/intern3/g' src/config.php

mkdir mysql
mkdir mysql/data
cp src/intern3_basic.sql mysql/data/intern3.sql
echo "FROM mariadb:10.1" > mysql/Dockerfile

mkdir php-apache

cat > php-apache/Dockerfile <<'_EOF'
FROM php:7.2-apache

RUN apt-get update --fix-missing
RUN apt-get upgrade -y
RUN apt-get install apt-utils wget dialog -y
RUN apt-get install curl git zip unzip zlib1g zlib1g-dev libicu-dev g++ libpng-dev libjpeg-dev libfreetype6-dev mysql-client vim build-essential libcurl3 libcurl3-dev openssl -y

RUN docker-php-ext-install mysqli
RUN docker-php-ext-install json
RUN docker-php-ext-install mbstring

RUN docker-php-ext-install pdo pdo_mysql intl

RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/
RUN docker-php-ext-install gd


COPY installer.sh /usr/local/bin/launcher.sh
RUN chmod +x /usr/local/bin/launcher.sh

CMD /usr/local/bin/launcher.sh
_EOF

cat > php-apache/installer.sh <<'_EOF'
#!/bin/bash

echo "Installerer Composer"

cd /var/www/html/
curl -sS https://getcomposer.org/installer | php && php composer.phar install

chown -R www-data:www-data /var/www/html
apache2 -DFOREGROUND
_EOF


cat > vhost.conf <<'_EOF'
<VirtualHost *:80>
	ServerName intern3.flyktig.no

	ServerAdmin webmaster@flyktig.no
    DocumentRoot /var/www/html/www

	ErrorLog ${APACHE_LOG_DIR}/error.log
	CustomLog ${APACHE_LOG_DIR}/access.log combined

</VirtualHost>
_EOF


cat > docker-compose.yml <<'_EOF'
version: '2'
services:
    php-apache:
        build:
            context: ./php-apache
        ports:
            - 8000:80
        volumes:
            - ./src:/var/www/html
        links:
            - 'mariadb'
        environment:
            APACHE_RUN_DIR: "/var/www/html"
            APACHE_RUN_USER: "www-data"
            APACHE_RUN_GROUP: "www-data"
            APACHE_LOG_DIR: "/var/www/html/www"
    mariadb:
        #        image: mysql:5.7
        build:
            context: ./mysql
        environment:
            - TZ=Europe/Rome
            - MYSQL_ALLOW_EMPTY_PASSWORD=no
            - MYSQL_ROOT_PASSWORD=intern3
            - MYSQL_USER=intern3
            - MYSQL_PASSWORD=intern3
            - MYSQL_DATABASE=intern3
        ports:
            - 8001:3306
        volumes:
            - ./mysql/data:/docker-entrypoint-initdb.d
_EOF

