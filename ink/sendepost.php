<?php

namespace intern3;

require_once("autolast.php");

/* Start utsending av e-post til de som har vakt innen de neste 24t */
$harVakt = BeboerListe::harVakt();
foreach($harVakt as $beboer){
    //Denne må legges til i tandem med knappen på profil-greia.
    if($beboer->vilHaVaktVarsler()){
        $vakterInnenDogn = $beboer->getVakterInnenDogn();

        foreach($vakterInnenDogn as $vakt){
            $beskjed = "Hei!\r\nDu har snart vakt! Du skal ha vakt " . $vakt->getDato() . "\r\nDette er " . $vakt->getVakttype();
            $beskjed .= "\r\n\r\n Med vennlig hilsen \r\nRobottene ved Internsidene";
            $beskjed .= "\r\n\r\nHvis denne e-posten er sendt feil, vennligst ta kontakt med data@singsaker.no";
            $epost = new \intern3\Epost($beskjed);
            $epost->addBrukerId($beboer->getBrukerId());
            $tittel = "[SINGSAKER-INTERN] Du skal snart sitte vakt!";
            $epost->send($tittel);
        }
    }
}
/* Slutt utsending av e-post til de som har vakt innen de neste 24t */




