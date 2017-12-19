<?php

namespace intern3;

require_once("../autolast.php");
setlocale(LC_ALL, 'nb_NO.utf-8');
date_default_timezone_set('Europe/Oslo');

set_time_limit(999999999);
$start = time();
echo "Endring av Vin-tabell startet ved " . date('Y-m-d H:i:s');
echo "\n";


$sql = "ALTER TABLE `vin` ADD `land` VARCHAR(255) NOT NULL DEFAULT 'udefinert' AFTER `slettet`;";

DB::getDB()->query($sql);




echo "FIN\n";
$slutt = time();
$tid = $slutt - $start;
echo "Brukte tid: $tid sekunder\n";
echo "Ferdig " . date('Y-m-d H:i:s');
echo "\n";
?>