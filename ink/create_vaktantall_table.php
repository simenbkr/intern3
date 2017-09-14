<?php

namespace intern3;

require_once("autolast.php");
setlocale(LC_ALL, 'nb_NO.utf-8');
date_default_timezone_set('Europe/Oslo');


set_time_limit(999999999);
$start = time();
echo "Opprettelse av vaktantall-tabell ved " . date('Y-m-d H:i:s');
echo "\n";

$sql = "CREATE TABLE `intern3`.`vaktantall` 
( `bruker_id` INT(8) NOT NULL , `semester` VARCHAR(128) NOT NULL , `antall` INT(32) NOT NULL,
PRIMARY KEY(bruker_id, semester)
)";


DB::getDB()->query($sql);


echo "FIN\n";
$slutt = time();
$tid = $slutt - $start;
echo "Brukte tid: $tid sekunder\n";
echo "Ferdig " . date('Y-m-d H:i:s');
echo "\n";
?>