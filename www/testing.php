<?php
namespace intern3;
require_once("../ink/autolast.php");



$beboerliste = BeboerListe::alle();

$st = DB::getDB()->prepare('CREATE TABLE epost_pref (
beboer_id INT(10) UNSIGNED NOT NULL,
tildelt TINYINT(1) NOT NULL DEFAULT 1,
snart_vakt TINYINT(1) NOT NULL DEFAULT 1,
bytte TINYINT(1) NOT NULL DEFAULT 1,
utleie TINYINT(1) NOT NULL DEFAULT 1,
barvakt TINYINT(1) NOT NULL DEFAULT 1
)');
$st->execute();

foreach($beboerliste as $beboer){
$st = DB::getDB()->prepare('INSERT INTO epost_pref(beboer_id,tildelt,snart_vakt,bytte,utleie,barvakt
) VALUES(
:beboer_id,1,1,1,1,1)');
$st->bindParam(':beboer_id',$beboer->getId());
$st->execute();
}

?>