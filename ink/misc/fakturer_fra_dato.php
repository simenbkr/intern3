<?php

namespace intern3;

require_once("../autolast_absolute.php");
setlocale(LC_ALL, 'nb_NO.utf-8');
date_default_timezone_set('Europe/Oslo');

$datoen = date('Y-m-d H:i:s', strtotime('2018-09-24 14:00:00'));

Krysseliste::fakturerOppTil($datoen);

