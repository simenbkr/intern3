<?php

namespace intern3;

require_once("/var/www/intern.singsaker.no/ink/autolast_absolute.php");

$year = date('Y');
$running_dates = array("$year-08-08", "$year-01-01");
$now = date('Y-m-d');

if(!in_array($now, $running_dates)) {
    exit("Ikke korrekt tidspunkt. Lukker");
}

$oppgaveListe = OppgaveListe::ikkeGodkjente();

foreach($oppgaveListe as $oppgave) {
    /* @var \intern3\Oppgave $oppgave */
    $oppgave->arkiver();
}

exit("Arkiverte gamle oppgaver");