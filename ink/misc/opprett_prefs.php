<?php
namespace intern3;

require_once("../autolast.php");
setlocale(LC_ALL, 'nb_NO.utf-8');
date_default_timezone_set('Europe/Oslo');

echo "######## Opprettelse av `prefs` ########";
echo "En tabell for oversikt over preferanser knyttet til div ting, i hovedsak om man vil stå på krysselister og pinkoder knyttet til dette.";


set_time_limit(999999999);
$start = time();
echo "Opprettelse og fylling av data til `prefs` startet ved " . date('Y-m-d H:i:s');
echo "\n";


$sql = "CREATE TABLE IF NOT EXISTS `prefs` (
beboerId INT(10) unsigned not null unique,
resepp TINYINT(1) DEFAULT 1,
vinkjeller TINYINT(1) DEFAULT 1,
pinboo TINYINT(1) DEFAULT 0,
pinkode VARCHAR(20) DEFAULT NULL,
vinpinboo TINYINT(1) DEFAULT 0,
vinpin VARCHAR(20) DEFAULT NULL
)";

$db = DB::getDB()->query($sql);

$db = DB::getDB();
foreach(BeboerListe::alle() as $beb){
    $query = "INSERT INTO prefs (beboerId, resepp, vinkjeller, pinboo, pinkode, vinpinboo, vinpin) VALUES(" . $beb->getId() . ",1,1,0,NULL,0,NULL)";
    $db->query($query);
}


echo "FIN\n";
$slutt = time();
$tid = $slutt - $start;
echo "Brukte tid: $tid sekunder\n";
echo "Ferdig " . date('Y-m-d H:i:s');
echo "\n";


?>