<?php

namespace intern3;

//require_once("/var/www/intern.singsaker.no/ink/autolast_absolute.php");
require_once ('autolast.php');

$vaktbytter = Vaktbytte::getAlleUtgatte();
$log = "---STARTLOG---\n";
$log .= "Startet ved " . date('r') . "\n";
foreach($vaktbytter as $vaktbytte){

    /* @var $vaktbytte \intern3\Vaktbytte */
    $st = DB::getDB()->prepare("DELETE FROM vaktbytte WHERE id=:id");
    $st->bindParam(':id', $vaktbytte->getId());
    //Venter med å execute til alt annet er gjort.
    $log .= "Vaktbytte: " . $vaktbytte->getId() . " for vakt " . $vaktbytte->getVaktId() . ", oppført på " .
        $vaktbytte->getVakt()->getBruker()->getPerson()->getFulltNavn();
    $log .= "\nSletter:";

    foreach($vaktbytte->getForslagVakter() as $forslag){
        /* @var $forslag \intern3\Vakt */
        $forslag->slettVaktbytteIdFraInstans($vaktbytte->getId());
        $log .= $forslag->getId() . ", ";
    }
    $log = rtrim($log, ', ');

    $st->execute();
    $log .= "Slettet forslag";
}

foreach(Vakt::alleVakterEtterDatoMedVaktbytte(date('Y-m-d')) as $vakt){
    /* @var $vakt \intern3\Vakt */
    $log .= "Fjerner ugyldige vaktbytter fra vaktid: " . $vakt->getId() . "\n";
    foreach($vakt->getVaktbytteDenneErMedI() as $id){
        if(Vaktbytte::medId($id) == null){
            $log .= "Sletter vaktbytteid $id\n";
            $vakt->slettVaktbytteIdFraInstans($id);
        }
    }
}
$log .= "Ferdig ved " . date('r') . "\n";
$log .= "---SLUTTLOG--\n";
echo $log;
Epost::sendEpost("data@singsaker.no", "[SING-VAKT] Slettet vaktbytter!", $log);

?>
