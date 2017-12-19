<?php
namespace intern3;

require_once("../autolast.php");
setlocale(LC_ALL, 'nb_NO.utf-8');
date_default_timezone_set('Europe/Oslo');


set_time_limit(999999999);
$start = time();
echo "Endring av bruker-tabell ved " . date('Y-m-d H:i:s');
echo "\n";

$sql = "ALTER TABLE `bruker` ADD `dato` VARCHAR(128) NOT NULL AFTER `salt`, ADD `glemt_token` VARCHAR(256) NOT NULL AFTER `dato`;";


DB::getDB()->query($sql);

echo "FIN\n";
$slutt = time();
$tid = $slutt - $start;
echo "Brukte tid: $tid sekunder\n";
echo "Ferdig " . date('Y-m-d H:i:s');
echo "\n";
?>