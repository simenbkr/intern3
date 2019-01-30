<?php

namespace intern3;
require_once("../autolast_absolute.php");
setlocale(LC_ALL, 'nb_NO.utf-8');
date_default_timezone_set('Europe/Oslo');


set_time_limit(999999999);
$start = time();
echo "Legger rader til Helga-tabellen for å fikse gjester per dager " . date('Y-m-d H:i:s');
echo "\n";

print "Er du sikker? [y/N]\n";
flush();
$confirmation = trim(fgets(STDIN));
if (!in_array($confirmation, array('y', 'Y', 'yes', 'YES', 'satan'))) {
    exit (0);
}

$sql  = "ALTER TABLE `helga` 
ADD `num_torsdag` INT(10) NOT NULL AFTER `max_gjest`, 
ADD `num_fredag` INT(10) NOT NULL AFTER `num_torsdag`, 
ADD `num_lordag` INT(10) NOT NULL AFTER `num_fredag`;";

DB::getDB()->query($sql);

$sql = "ALTER TABLE `helga` ADD `same` TINYINT(1) NOT NULL AFTER `num_lordag`;";

DB::getDB()->query($sql);


echo "FIN\n";
$slutt = time();
$tid = $slutt - $start;
echo "Brukte tid: $tid sekunder\n";
echo "Ferdig " . date('Y-m-d H:i:s');
echo "\n";



