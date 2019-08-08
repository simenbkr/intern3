<?php


namespace intern3;

require_once("/srv/http/intern3/ink/autolast_absolute.php");
//require_once("/var/www/intern.singsaker.no/ink/autolast_absolute.php");

$oppgaveListe = OppgaveListe::ikkeGodkjente();

foreach($oppgaveListe as $oppgave) {
    /* @var \intern3\Oppgave $oppgave */
    $oppgave->arkiver();
}