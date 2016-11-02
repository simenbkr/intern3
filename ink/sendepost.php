<?php

namespace intern3;

require_once("autolast.php");

/* Start utsending av e-post til de som har vakt innen de neste 24t */
$harVakt = BeboerListe::harVakt();
foreach($harVakt as $beboer){
    if($beboer->vilHaVaktVarsler()){
        $vakterInnenDogn = $beboer->getVakterInnenDogn();

        foreach($vakterInnenDogn as $vakt){
            $beskjed = "<html><body>Hei!<br/>Du har snart vakt! Du skal ha vakt " . $vakt->getDato() . "<br/>Dette er " . $vakt->getVakttype();
            $beskjed .= "<br/><br/> Med vennlig hilsen <br/>Robottene ved Internsidene";
            $beskjed .= "<br/><br/>Hvis denne e-posten er sendt feil, vennligst ta kontakt med data@singsaker.no</body></html>";
            $epost = new \intern3\Epost($beskjed);
            $epost->addBrukerId($beboer->getBrukerId());
            $tittel = "[SING-INTERN] Du skal snart sitte vakt!";
            $epost->send($tittel);
        }
    }
}
/* Slutt utsending av e-post til de som har vakt innen de neste 24t */




