<?php

namespace intern3;

require_once("../autolast_absolute.php");
setlocale(LC_ALL, 'nb_NO.utf-8');
date_default_timezone_set('Europe/Oslo');

$output = 'Navn,E-post,Telefon,Adresse,Postnummer,Antall semestere,Innflyttingsdato' . "\n";


function beboer2csv(Beboer $beboer){
    
    $ret  = '';
    $ret .= str_replace(',','.',$beboer->getFulltNavn()) . ',';
    $ret .= str_replace(',','.',$beboer->getEpost()) . ',';
    $ret .= str_replace(',','.',$beboer->getTelefon()) . ',';
    $ret .= str_replace(',','.',$beboer->getAdresse()) . ',';
    $ret .= str_replace(',','.',$beboer->getPostnummer()) . ',';
    $ret .= str_replace(',','.',$beboer->getRomhistorikk()->getAntallSemestre()) . ',';
    $ret .= str_replace(',','.',$beboer->getRomhistorikk()->getPerioder()[0]->innflyttet);
    return $ret;
}


foreach(BeboerListe::alle() as $beboer){
    $output .= beboer2csv($beboer) . "\n";
}

file_put_contents("beboerliste.csv", $output);






