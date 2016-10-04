<?php
/**
 * Created by PhpStorm.
 * User: Simen
 * Date: 04.10.2016
 * Time: 18:46
 */

require_once("autolast.php");

//Sender epost til de som skal sitte vakt innen de neste 24t:
$harVakt = \intern3\BeboerListe::harVakt();
foreach($harVakt as $beboer){
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





