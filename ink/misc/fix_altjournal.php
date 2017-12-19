<?php


namespace intern3;

require_once("../autolast.php");
setlocale(LC_ALL, 'nb_NO.utf-8');
date_default_timezone_set('Europe/Oslo');
//Flytte fra Journal til AltJournal.
set_time_limit(999999999);
$start = time();
echo "Fiksing av alt_journal startet ved " . date('Y-m-d H:i:s');
echo "\n";


$st = DB::getDB()->prepare('SELECT * FROM journal ORDER BY kryss_id ASC');
$st->execute();
$radene = $st->fetchAll();

foreach($radene as $rad){

    $st = DB::getDB()->prepare('UPDATE alt_journal SET bruker_id=:brukerid,vakt=:vakt WHERE dato=:dato');

    //$id = $rad['kryss_id'] - 90;

    if(($beboer = Beboer::medId($rad['beboer_id'])) == null){
        $bruker_id = Ansatt::getSisteAnsatt()->getBrukerId();
    } else {
        $bruker_id = $beboer->getBrukerId();
    }

    $vakt = $rad['vakt'];

    $st->bindParam(':brukerid', $bruker_id);
    $st->bindParam(':vakt', $vakt);
    $st->bindParam(':dato', $rad['dato']);

    $st->execute();

}

echo "FIN\n";
$slutt = time();
$tid = $slutt - $start;
echo "Brukte tid: $tid sekunder\n";
echo "Ferdig " . date('Y-m-d H:i:s');
echo "\n";
?>
