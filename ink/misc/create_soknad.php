<?php

namespace intern3;
require_once("../autolast_absolute.php");
setlocale(LC_ALL, 'nb_NO.utf-8');
date_default_timezone_set('Europe/Oslo');


set_time_limit(999999999);
$start = time();
echo "Opprettelse av Søknadstabell ved " . date('Y-m-d H:i:s');
echo "\n";

print "Er du sikker? [y/N]\n";
flush();
$confirmation  =  trim( fgets( STDIN ) );
if ( !in_array($confirmation, array('y', 'Y')) ) {
    exit (0);
}


$sql = "CREATE TABLE `soknad` 
( `id` INT NOT NULL AUTO_INCREMENT , 
`innsendt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , 
`navn` VARCHAR(128)  , 
`adresse` VARCHAR(128) , 
`epost` VARCHAR(128)  , 
`telefon` VARCHAR(20)  ,
`fodselsar` INT(8)  , 
`skole` VARCHAR(128) , 
`studie` VARCHAR(128)  , 
`fagbrev` TINYINT(1) , 
`kompetanse` TEXT , 
`kjennskap` TEXT , 
`kjenner` TEXT  , 
`tekst` TEXT , 
`bilde` VARCHAR(256), 
PRIMARY KEY (`id`))";

DB::getDB()->query($sql);

echo "FIN\n";
$slutt = time();
$tid = $slutt - $start;
echo "Brukte tid: $tid sekunder\n";
echo "Ferdig " . date('Y-m-d H:i:s');
echo "\n";