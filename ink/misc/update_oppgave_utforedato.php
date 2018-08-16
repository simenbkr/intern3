<?php


namespace intern3;
require_once("../autolast_absolute.php");
setlocale(LC_ALL, 'nb_NO.utf-8');
date_default_timezone_set('Europe/Oslo');


set_time_limit(999999999);
$start = time();
echo "Endring av oppgave-tabellen til å inkludere utførelsesdato." . date('Y-m-d H:i:s');
echo "\n";

print "Er du sikker? [y/N]\n";
flush();
$confirmation  =  trim( fgets( STDIN ) );
if ( !in_array($confirmation, array('y', 'Y')) ) {
    exit (0);
}

$sql = "ALTER TABLE `oppgave` ADD `tid_utfore` DATE NULL DEFAULT NULL AFTER `status`;";

DB::getDB()->query($sql);

echo "FIN\n";
$slutt = time();
$tid = $slutt - $start;
echo "Brukte tid: $tid sekunder\n";
echo "Ferdig " . date('Y-m-d H:i:s');
echo "\n";

