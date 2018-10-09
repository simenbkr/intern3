#!/bin/bash

#phpmyadmin-pakka inneholder alle php-relaterte dependencies, og er et ganske greit verktøy for debugging.
echo "Installerer nødvendige pakker."
apt install phpmyadmin php-intl
echo "Installerer Composer"
curl -sS https://getcomposer.org/installer | php && php composer.phar install

echo "Setter opp database"
mysql -u intern3 -p intern3_dev < intern3_basic.sql

echo "Du kan nå logge inn med e-post test@testesen.no, og passord testetest."




