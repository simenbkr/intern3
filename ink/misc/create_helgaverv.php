<?php

namespace intern3;
require_once("../autolast_absolute.php");
setlocale(LC_ALL, 'nb_NO.utf-8');
date_default_timezone_set('Europe/Oslo');


set_time_limit(999999999);
$start = time();
echo "Opprettelse av Helgaverv-tabell ved " . date('Y-m-d H:i:s');
echo "\n";


$sql = "CREATE TABLE `helgaverv`
( `id` INT NOT NULL AUTO_INCREMENT , 
`navn` INT NOT NULL , 
`tilgang` INT NOT NULL , 
PRIMARY KEY (`id`)) ";

DB::getDB()->query($sql);


echo "FIN\n";
$slutt = time();
$tid = $slutt - $start;
echo "Brukte tid: $tid sekunder\n";
echo "Ferdig " . date('Y-m-d H:i:s');
echo "\n";
?>