<?php

namespace intern3;
require_once("../autolast_absolute.php");
setlocale(LC_ALL, 'nb_NO.utf-8');
date_default_timezone_set('Europe/Oslo');


set_time_limit(999999999);
$start = time();
echo "Fikser romtyper " . date('Y-m-d H:i:s');
echo "\n";

print "Er du sikker? [y/N]\n";
flush();
$confirmation = trim(fgets(STDIN));
if (!in_array($confirmation, array('y', 'Y', 'yes', 'YES', 'satan'))) {
    exit (0);
}

$storhybler = array('212', '100', '112', '240', '241', '243', '242', '152', '252', '160', '260', '163', '263', '162', '262', '164', '264', '165', '265', '254', '245');
$korr = array('101', '239', '244', '271', '171', '270', '170');
$lper = array('111','211', '107','105','207','205','255','155','157','257','161','261');
$sper = array('201', '151', '251', '242');

$lp = Romtype::medNavn('Liten Parhybel');
$sp = Romtype::medNavn('Stor Parhybel');
$bt = Romtype::medNavn('Bøttekott');
$sh = Romtype::medNavn('Storhybel');
$kh = Romtype::medNavn('Korrhybel');

$st = DB::getDB()->prepare('UPDATE rom SET romtype_id=:rid');
$st->execute([':rid' => $bt->getId()]);

$st = DB::getDB()->prepare('UPDATE rom SET romtype_id=:rid WHERE navn=:navn');
$romtyper = array($sh, $kh, $lp, $sp);
foreach(array($storhybler, $korr, $lper, $sper) as $indeks => $romliste) {
    $romtypen = $romtyper[$indeks];
    $st->bindParam(':rid', $romtypen->getId());
    foreach($romliste as $romnavn) {
        $st->bindParam(':navn', $romnavn);
        $st->execute();
    }
}





echo "FIN\n";
$slutt = time();
$tid = $slutt - $start;
echo "Brukte tid: $tid sekunder\n";
echo "Ferdig " . date('Y-m-d H:i:s');
echo "\n";

