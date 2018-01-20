<?php

namespace intern3;

require_once("../autolast_absolute.php");
setlocale(LC_ALL, 'nb_NO.utf-8');
date_default_timezone_set('Europe/Oslo');


set_time_limit(999999999);
$start = time();
echo "Endring av vinkryss-tabell ved " . date('Y-m-d H:i:s');
echo "\n";


$sql = "ALTER TABLE `vinkryss` ADD `ansvarlig` INT(10) NOT NULL AFTER `prisen`;";

DB::getDB()->query($sql);

$sql = "UPDATE `vinkryss` SET ansvarlig = beboerId";

DB::getDB()->query($sql);


echo "FIN\n";
$slutt = time();
$tid = $slutt - $start;
echo "Brukte tid: $tid sekunder\n";
echo "Ferdig " . date('Y-m-d H:i:s');
echo "\n";