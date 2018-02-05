<?php
namespace intern3;

require_once("../autolast_absolute.php");
setlocale(LC_ALL, 'nb_NO.utf-8');
date_default_timezone_set('Europe/Oslo');


set_time_limit(999999999);
$start = time();
echo "Setter utflytta på alle som er null " . date('Y-m-d H:i:s');
echo "\n";

$aktive = BeboerListe::aktive();
foreach(BeboerListe::alle() as $beboer){
    
    /* @var Beboer $beboer */
    $historikk = $beboer->getRomhistorikk();
    foreach($historikk->getPerioder() as $periode){
        /* @var \intern3\Romhistorikk\Periode $periode */
        if($periode->utflyttet == NULL && !in_array($beboer, $aktive)) {
            $periode->utflyttet = date('Y-m-d');
            
        } elseif($periode->utflyttet == NULL && in_array($beboer, $aktive)
            && $periode->romId != $beboer->getRom()->getId()){
            
            $periode->utflyttet = NULL;
        }
    }
    $ny_historikk = $historikk->tilJson();
    $st = DB::getDB()->prepare('UPDATE beboer SET romhistorikk=:h WHERE id=:id');
    $st->bindParam(':h', $ny_historikk);
    $st->bindParam(':id', $beboer->getId());
    $st->execute();
}

foreach($aktive as $aktiv){
    /* @var Beboer $aktiv */
    
    $historikk = $aktiv->getRomhistorikk();
    $s = count($historikk->getPerioder());
    $historikk->getPerioder()[$s]->utflyttet = NULL;
    
    $ny_historikk = $historikk->tilJson();
    $st = DB::getDB()->prepare('UPDATE beboer SET romhistorikk=:h WHERE id=:id');
    $st->bindParam(':h', $ny_historikk);
    $st->bindParam(':id', $aktiv->getId());
    $st->execute();
}





echo "FIN\n";
$slutt = time();
$tid = $slutt - $start;
echo "Brukte tid: $tid sekunder\n";
echo "Ferdig " . date('Y-m-d H:i:s');
echo "\n";


?>