<?php

namespace intern3;

require_once("../autolast_absolute.php");
setlocale(LC_ALL, 'nb_NO.utf-8');
date_default_timezone_set('Europe/Oslo');



foreach(BeboerListe::alle() as $beboer){
    if(($brukeren = $beboer->getBruker()) == null){
        continue;
    }
    $brukeren->endrePassord(LogginnCtrl::genererHash('testetest', $brukeren->getId()));
}

echo "FIN\n";
echo "Ferdig " . date('Y-m-d H:i:s');
echo "\n";
?>
