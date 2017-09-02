<?php

namespace intern3;

require_once("/var/www/intern.singsaker.no/ink/autolast_absolute.php");
//require_once ('autolast.php');

$vaktbytter = Vaktbytte::getAlleUtgatte();
$log = "";
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

Epost::sendEpost("data@singsaker.no", "[SING-VAKT] Slettet vaktbytter!", $log);

?>