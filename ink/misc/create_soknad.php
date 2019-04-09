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
`navn` VARCHAR(128) NOT NULL , 
`adresse` VARCHAR(128) NOT NULL , 
`epost` VARCHAR(128) NOT NULL , 
`telefon` VARCHAR(20) NOT NULL ,
`fodselsar` INT(8) NOT NULL , 
`skole` VARCHAR(128) NOT NULL , 
`studie` VARCHAR(128) NOT NULL , 
`fagbrev` TINYINT(1) NOT NULL , 
`kompetanse` TEXT NOT NULL , 
`kjennskap` TEXT NOT NULL , 
`kjenner` TEXT NOT NULL , 
`tekst` TEXT NOT NULL , 
`bilde` VARCHAR(256) NOT NULL , 
PRIMARY KEY (`id`))";

DB::getDB()->query($sql);

echo "FIN\n";
$slutt = time();
$tid = $slutt - $start;
echo "Brukte tid: $tid sekunder\n";
echo "Ferdig " . date('Y-m-d H:i:s');
echo "\n";