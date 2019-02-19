<?php

namespace intern3;

require_once("/var/www/intern.singsaker.no/ink/autolast_absolute.php");
$df = new \IntlDateFormatter('nb_NO',
    \IntlDateFormatter::TRADITIONAL, \IntlDateFormatter::NONE,
    'Europe/Oslo');

$imorgen = strtotime("+1 day");
$dato = date('Y-m-d', $imorgen);

$vakter_i_morgen = Vakt::getVakterByDato($dato);

$mottakere = array();
foreach($vakter_i_morgen as $vakt){
    /* @var $vakt \intern3\Vakt */
    if($vakt->getBruker() == null || ($beboeren = $vakt->getBruker()->getPerson()) == null){
        continue;
    }

    if(!$beboeren instanceof Beboer){
        continue;
    }

    if($beboeren->vilHaVaktVarsler()){
        $beskjed = "<html><body>Hei!<br/>Du har snart vakt! Du skal ha " . $vakt->toString() . "<br/>";
        $beskjed .= "<br/><br/> Med vennlig hilsen <br/>Robotene ved Internsidene";
        $beskjed .= "<br/><br/>Hvis denne e-posten er sendt feil, vennligst ta kontakt med data@singsaker.no</body></html>";

        $tittel = "[SING-VAKT] Du skal snart sitte vakt!";

        Epost::sendEpost($beboeren->getEpost(), $tittel, $beskjed);
        $mottakere[] = $beboeren->getEpost();
    }
}

$beskjed = "Halla, har sendt ut epost til disse: <br/>" . json_encode($mottakere, true);
Epost::sendEpost("data@singsaker.no", "[SING-VAKT] Har sendt ut vakt-varsel!", $beskjed);