<?php

namespace intern3;

require_once("autolast.php");
$df = new IntlDateFormatter('nb_NO',
    IntlDateFormatter::TRADITIONAL, IntlDateFormatter::NONE,
    'Europe/Oslo');

/* Start utsending av e-post til de som har vakt innen de neste 24t */
/* Kjøres hver dag klokken 13:00 (cronjobb) */
$Beboerliste = BeboerListe::aktive();
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

/* Start utsending 24t til barvakt og barvakt er ledig! */

foreach(Utleie::getUtleierFremover() as $utleiet){
    $utleie_dato = $utleiet->getDato();
    $dagens_dato = date('Y-m-d');
    $differens = floor(($utleie_dato - $dagens_dato)/(60*60*24));
    if($differens == 1){
        $mottakere = "";
        foreach ($Beboerliste as $beboer){
            if($beboer->vilHaBarvaktVarsel() && in_array($beboer, $utleiet->getBarvakter())){
                $mottakere .= $beboer->getEpost() . ",";
            }
        }
        $mottakere = rtrim($mottakere, ',');
        $mottakere = "data@singsaker.no";
        $tittel = "[SING-INTERN] Det er 24t igjen til du skal stå barvakt!";
        $tekst = "<html>(Dette er en automatisert melding)<br/><br/>Du er meldt opp som barvakt for utleiet til " . $utleiet->getNavn() .
            ", " . $df->format($utleiet->getDato()) . " i " . $utleiet->getRom() . "<br/><br/><br/></html>";
        Epost::sendEpost($mottakere,$tittel,$tekst);
    }
}
/* Slutt utsending 24t til barvakt og barvakt er ledig! */