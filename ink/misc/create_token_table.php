<?php

namespace intern3;

require_once("../autolast_absolute.php");
setlocale(LC_ALL, 'nb_NO.utf-8');
date_default_timezone_set('Europe/Oslo');


set_time_limit(999999999);
$start = time();
echo "Opprettelse av token-tabell ved " . date('Y-m-d H:i:s');
echo "\n";


$sql = "CREATE TABLE IF NOT EXISTS `token`(
token VARCHAR(190) NOT NULL UNIQUE,
type VARCHAR(100) NOT NULL,
duration VARCHAR(30) NOT NULL,
time_issued VARCHAR(70)
)";


DB::getDB()->query($sql);


echo "FIN\n";
$slutt = time();
$tid = $slutt - $start;
echo "Brukte tid: $tid sekunder\n";
echo "Ferdig " . date('Y-m-d H:i:s');
echo "\n";
?>