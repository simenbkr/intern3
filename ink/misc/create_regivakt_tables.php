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

//======================================================================================================================

$sql = "CREATE TABLE `regivakt`(
 `id` INT(10) NOT NULL AUTO_INCREMENT ,
`dato` DATE NOT NULL , 
`start_tid` VARCHAR(255) NULL , 
`slutt_tid` VARCHAR(255) NULL , 
`beskrivelse` TEXT NULL , 
`nokkelord` TEXT NULL , 
`antall` INT(10) NOT NULL , 
`status` INT(10) NOT NULL , 
`bytte` TINYINT(1) NULL DEFAULT '0'
PRIMARY KEY (`id`));";


$db = DB::getDB();
$db->query($sql);

$sql = "CREATE TABLE `regivakt_bytte` ( 
`id` INT(10) NOT NULL AUTO_INCREMENT , 
`bruker_id` INT(10) NULL , 
`regivakt_id` INT(10) NULL , 
`gisbort` TINYINT(1) NULL DEFAULT '0' , 
`passord` VARCHAR(512) NULL , 
`forslag` TEXT NULL , 
`merknad` TEXT NULL , 
`slipp` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , 
PRIMARY KEY (`id`));";


$db->query($sql);

//======================================================================================================================

echo "FIN\n";
$slutt = time();
$tid = $slutt - $start;
echo "Brukte tid: $tid sekunder\n";
echo "Ferdig " . date('Y-m-d H:i:s');
echo "\n";

