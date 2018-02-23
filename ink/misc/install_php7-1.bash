#!/bin/bash

apt install php7.1 php7.1-mysql php7.1-intl
a2dismod php7.0
a2enmod php7.1
systemctl apache2 restart
