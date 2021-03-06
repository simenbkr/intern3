<?php

namespace intern3;
require_once("../autolast_absolute.php");
setlocale(LC_ALL, 'nb_NO.utf-8');
date_default_timezone_set('Europe/Oslo');


set_time_limit(999999999);
$start = time();
echo "Opprettelse av Helgaverv-tabell ved " . date('Y-m-d H:i:s');
echo "\n";

print "Er du sikker? [y/N]\n";
flush();
$confirmation  =  trim( fgets( STDIN ) );
if ( !in_array($confirmation, array('y', 'Y')) ) {
    exit (0);
}


$sql = "CREATE TABLE `helgaverv`
( `id` INT NOT NULL AUTO_INCREMENT , 
`navn` VARCHAR(512) NOT NULL , 
`tilgang` INT NOT NULL , 
PRIMARY KEY (`id`)) ";

DB::getDB()->query($sql);

$sql = "INSERT INTO helgaverv (navn,tilgang) VALUES('Sikkerhetssjef', 1)";

DB::getDB()->query($sql);


$sql = "CREATE TABLE `helgaverv_beboer`
( `id` INT NOT NULL, 
`beboer_id` INT NOT NULL)";

DB::getDB()->query($sql);


echo "FIN\n";
$slutt = time();
$tid = $slutt - $start;
echo "Brukte tid: $tid sekunder\n";
echo "Ferdig " . date('Y-m-d H:i:s');
echo "\n";
?>