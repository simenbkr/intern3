<?php

namespace intern3;
require_once("../autolast_absolute.php");

$db = DB::getDB();

$db->query('TRUNCATE TABLE arbeid');
$db->query('TRUNCATE TABLE arbeid_bilder');
$db->query('TRUNCATE TABLE helgagjest');
$db->query('TRUNCATE TABLE journal');
$db->query('TRUNCATE TABLE oppgave');
$db->query('TRUNCATE TABLE rapport');
$db->query('TRUNCATE TABLE soknad');
$db->query('TRUNCATE TABLE storhybel');
$db->query('TRUNCATE TABLE storhybel_fordeling');
$db->query('TRUNCATE TABLE storhybel_rekkefolge');
$db->query('TRUNCATE TABLE storhybel_rom');
$db->query('TRUNCATE TABLE storhybel_velger');
$db->query('TRUNCATE TABLE token');
$db->query('TRUNCATE TABLE utleie');

$i = 1;
$databeboer = Bruker::medEpost('data@singsaker.no');
foreach(BeboerListe::alle() as $beboer){
    /* @var \intern3\Beboer $beboer */

    if(($brukeren = $beboer->getBruker()) == null){
        continue;
    }
    $brukeren->endrePassord(LogginnCtrl::genererHash('testetest', $brukeren->getId()));
    $beboer->setFornavn('Person');
    $beboer->setMellomnavn('');
    $beboer->setEtternavn($i);
    $beboer->setEpost('person-' . $i++ . '@singsaker.no');
    $beboer->setTelefon('12345678');
}


$databeboer->getPerson()->setEpost('data@singsaker.no');
$databeboer->getPerson()->setFornavn('admin');

foreach(BeboerListe::ikkeAktive() as $beboer) {
    $beboer->setFornavn('Person');
    $beboer->setMellomnavn('');
    $beboer->setEtternavn($i);
    $beboer->setEpost('person-' . $i++ . '@singsaker.no');
    $beboer->setTelefon('12345678');
}