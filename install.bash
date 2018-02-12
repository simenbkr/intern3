#!/bin/bash

#phpmyadmin-pakka inneholder alle php-relaterte dependencies, og er et ganske greit verktøy for debugging.
apt install phpmyadmin php-intl
curl -sS https://getcomposer.org/installer | php && php composer.phar install

