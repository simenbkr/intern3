<?php

namespace intern3;
require_once("../autolast_absolute.php");
setlocale(LC_ALL, 'nb_NO.utf-8');
date_default_timezone_set('Europe/Oslo');


set_time_limit(999999999);
$start = time();
echo "Oppretter tabeller til Storhybellista " . date('Y-m-d H:i:s');
echo "\n";

print "Er du sikker? [y/N]\n";
flush();
$confirmation = trim(fgets(STDIN));
if (!in_array($confirmation, array('y', 'Y'))) {
    exit (0);
}


$sql = "CREATE TABLE `storhybel` 
( `id` INT NOT NULL AUTO_INCREMENT ,
 `semester` VARCHAR(128) NOT NULL ,
  `navn` VARCHAR(128) NOT NULL ,
   PRIMARY KEY (`id`))";


DB::getDB()->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS `storhybel_rom` 
( `storhybel_id` INT NOT NULL ,
 `rom_id` INT NOT NULL )";

DB::getDB()->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS `storhybel_rekkefolge` 
( `storhybel_id` INT NOT NULL ,
 `beboer_id` INT NOT NULL ,
  `nummer` INT NOT NULL ) ";

DB::getDB()->query($sql);

echo "FIN\n";
$slutt = time();
$tid = $slutt - $start;
echo "Brukte tid: $tid sekunder\n";
echo "Ferdig " . date('Y-m-d H:i:s');
echo "\n";


