<?php
namespace intern3;
require_once ('../ink/phpqrcode.php');
require_once("../ink/autolast.php");
setlocale(LC_ALL, 'nb_NO.utf-8');date_default_timezone_set('Europe/Oslo');

$meg = Bruker::medEpost('simen@haitech.org');

$alles_passord = 'testetest';

$st = DB::getDB()->prepare('SELECT id FROM bruker');
$st->execute();

for($i = 0; $i < $st->rowCount(); $i++){
    $brukeren = Bruker::medId($st->fetch()['id']);
    $brukeren->endreSalt(Funk::generatePassword(28));
    $brukeren->endrePassord(LogginnCtrl::genererHash($alles_passord, $brukeren->getId()));
}
echo "FIN";
?>