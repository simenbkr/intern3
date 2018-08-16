<?php

namespace intern3;

require_once("/var/www/intern.singsaker.no/ink/autolast_absolute.php");
$df = new \IntlDateFormatter('nb_NO',
    \IntlDateFormatter::TRADITIONAL, \IntlDateFormatter::NONE,
    'Europe/Oslo');

$imorgen = strtotime("+1 day");
$dato = date('Y-m-d', $imorgen);

$oppgaver_i_morgen = Oppgave::getOppgaverByUtforelseDato($dato);
$sendt_til = array();

foreach($oppgaver_i_morgen as $oppgave){
    /* @var \intern3\Oppgave $oppgave */
    
    $tittel = "[SING-INTERN] Du har en regi-oppgave i morgen!";
    $beskjed = "<html><body>Hei!<br/><br/>Dette er en påminnelse om at du er satt opp på en regi-oppgave i morgen. Beskrivelse følger:<br/><br/>";
    $beskjed .= "<h3>" . $oppgave->getNavn() . "</h3><br/>" . $oppgave->getBeskrivelse();
    $beskjed .= "<br/><br/>Med vennlig hilsen <br/>Singsaker Internside";
    $beskjed .= "<br/><br/>Hvis denne e-posten er sendt feil, vennligst ta kontakt med data@singsaker.no</body></html>";
    
    
    foreach($oppgave->getPameldteBeboere() as $beboer){
        /* @var \intern3\Beboer $beboer */
        
        if($beboer->vilHaVaktVarsler()){
            Epost::sendEpost($beboer->getEpost(), $tittel, $beskjed);
            $sendt_til[] = $beboer;
        }
        
    }
    
    
}

if(count($sendt_til) > 1) {
    $beskjed = "Halla, har sendt ut epost til disse: <br/>" . json_encode($sendt_til, true);
    Epost::sendEpost("data@singsaker.no", "[SING-REGI] Har sendt ut regi-varsel!", $beskjed);
}