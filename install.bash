#!/bin/bash

echo "Installerer nødvendige pakker."
sudo apt install php7.2 php7.2-intl php7.2-gd libapache2-mod-php7.2 php7.2-mysql
echo "Installerer Composer"
curl -sS https://getcomposer.org/installer | php && php composer.phar install

echo "Setter opp database"

echo -n "Root-password til MySQL: " 
read -s password
echo

echo "CREATE USER 'intern3'@'localhost' IDENTIFIED BY 'intern3';" | mysql -u root -p$password
echo "GRANT ALL PRIVILEGES ON intern3 . * TO 'intern3'@'localhost';" | mysql -u root -p$password
echo "GRANT ALL PRIVILEGES ON intern3_dev . * TO 'intern3'@'localhost';" | mysql -u root -p$password
echo "flush privileges" | mysql -u root -p$password
echo "CREATE DATABASE intern3;" | mysql -u root -p$password
echo "CREATE DATABASE intern3_dev;" | mysql -u root -p$password

mysql -u intern3 -pintern3 intern3_dev < intern3_basic.sql

cp config_example.php config.php
sudo chown -R www-data:www-data .
sudo systemctl restart apache2

echo "Sjekk at config.php er riktig konfigurert."
echo ""
echo "Du kan deretter logge inn med e-post test@testesen.no, og passord testetest."
