<?php
namespace intern3;
require_once("../ink/autolast.php");
setlocale(LC_ALL, 'nb_NO.utf-8');date_default_timezone_set('Europe/Oslo');


$vanlige_vakter = VaktListe::autogenerertVanligVakt();
$forstevakter = VaktListe::autogenerertForstevakt();
$fullvakt = BeboerListe::fullVakt();
$halvvakt = BeboerListe::halvVakt();
$maks_kjipe = ceil(Vakt::antallKjipeAutogenererte() / (count($fullvakt) + count($halvvakt))) + 1;

echo $maks_kjipe;

$i = 0;

foreach(BeboerListe::harVakt() as $beboer){
    /* @var \intern3\Beboer $beboer */
    if($beboer->antallKjipeVakter() > $maks_kjipe){
        $i++;
        echo "<b>" . $beboer->getFulltNavn() . ":" . $beboer->antallKjipeVakter() . ":" . $beboer->getBruker()->antallVakterErOppsatt() . "</b><br/>";
    } else {
        echo $beboer->getFulltNavn() . ":" . $beboer->antallKjipeVakter() . ":" . $beboer->getBruker()->antallVakterErOppsatt() . "<br/>";
    }
}

echo "$i har > $maks_kjipe kjipte vakter av " . count(BeboerListe::harVakt()) . " som har vakt.";


$num = 0;
$tot = 0;
foreach(Vakt::alleVakterEtterDato(date('Y-m-d')) as $vakt){
    $tot++;
    if($vakt->erKjip()){
        $num++;
    }
}

echo "<br/>antall kjipe vakter: $num av totalt: $tot";

?>