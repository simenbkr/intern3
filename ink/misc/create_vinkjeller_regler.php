<?php

namespace intern3;
require_once("../autolast_absolute.php");
setlocale(LC_ALL, 'nb_NO.utf-8');
date_default_timezone_set('Europe/Oslo');


set_time_limit(999999999);
$start = time();
echo "Opprettelse av Vinkjeller-regler-tabell ved " . date('Y-m-d H:i:s');
echo "\n";

print "Er du sikker? [y/N]\n";
flush();
$confirmation  =  trim( fgets( STDIN ) );
if ( !in_array($confirmation, array('y', 'Y')) ) {
exit (0);
}

$sql = "CREATE TABLE `vin_regler`
( `id` INT NOT NULL AUTO_INCREMENT ,
`tekst` TEXT NOT NULL ,
PRIMARY KEY (`id`))";

DB::getDB()->query($sql);

DB::getDB()->query('INSERT INTO vin_regler (tekst) VALUES("hmm")');

print "Ferdig!";