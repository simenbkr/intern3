<?php

namespace intern3;

require_once("../autolast_absolute.php");
setlocale(LC_ALL, 'nb_NO.utf-8');
date_default_timezone_set('Europe/Oslo');
//Flytte fra Journal til AltJournal.
set_time_limit(999999999);
$start = time();
echo "Migrering av journal til alt_journal startet ved " . date('Y-m-d H:i:s');
echo "\n";

$st = DB::getDB()->prepare('SELECT * FROM journal ORDER BY kryss_id ASC');
$st->execute();
$radene = $st->fetchAll();
foreach($radene as $rad){
$st_1 = DB::getDB()->prepare('INSERT INTO alt_journal (bruker_id,vakt,dato,status) VALUES(:bruker_id,:vakt,:dato,:status)');
$st_1->bindParam(':vakt', $rad['vakt']);
$st_1->bindParam(':dato', $rad['dato']);

if(($vakthavende = Beboer::medId($rad['beboer_id'])) == null){
$vakthavende = Ansatt::getSisteAnsatt();
}
$bruker_id = $vakthavende->getBrukerId();
$st_1->bindParam(':bruker_id', $bruker_id);

//{"drikkeId":1,"mottatt":5,"avlevert":5,"pafyll":0,"utavskap":0}]
$ol = array(
'drikkeId' => 2,
'mottatt' => $rad['ol_mottatt'],
'pafyll' => $rad['ol_pafyll'],
'avlevert' => $rad['ol_avlevert'],
'utavskap' => $rad['ol_utavskap']
);

$cid = array(
'drikkeId' => 3,
'mottatt' => $rad['cid_mottatt'],
'pafyll' => $rad['cid_pafyll'],
'avlevert' => $rad['cid_avlevert'],
'utavskap' => $rad['cid_utavskap']
);

$carls = array(
'drikkeId' => 4,
'mottatt' => $rad['carls_mottatt'],
'pafyll' => $rad['carls_pafyll'],
'avlevert' => $rad['carls_avlevert'],
'utavskap' => $rad['carls_utavskap']
);

$rikdom = array(
'drikkeId' => 5,
'mottatt' => $rad['rikdom_mottatt'],
'pafyll' => $rad['rikdom_pafyll'],
'avlevert' => $rad['rikdom_avlevert'],
'utavskap' => $rad['rikdom_utavskap']
);

$status_objekt = json_encode(array($ol,$cid,$carls,$rikdom),true);

$st_1->bindParam(':status', $status_objekt);
$st_1->execute();

}

echo "FIN\n";
$slutt = time();
$tid = $slutt - $start;
echo "Brukte tid: $tid sekunder\n";
echo "Ferdig " . date('Y-m-d H:i:s');
echo "\n";
?>