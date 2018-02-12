#!/bin/bash

#phpmyadmin-pakka inneholder alle php-relaterte dependencies, og er et ganske greit verktøy for debugging.
echo "Installerer nødvendige pakker."
apt install phpmyadmin php-intl
echo "Installerer Composer"
curl -sS https://getcomposer.org/installer | php && php composer.phar install

echo "Setter opp database"
mysql -u intern3 -p intern3 < db/2017-09-28-intern3.sql
echo "Kjører PHP-skript for å ferdiggjøre database"
cd ink/misc && for f in *.php; php "$f"; done





