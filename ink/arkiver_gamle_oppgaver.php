<?php

namespace intern3;

require_once("/var/www/intern.singsaker.no/ink/autolast_absolute.php");

$year = date('Y');
$running_dates = array("$year-08-01", "$year-01-01");
$now = date('Y-m-d');

if(!in_array($now, $running_dates)) {
    exit("Ikke korrekt tidspunkt. Lukker");
}

$oppgaveListe = OppgaveListe::ikkeGodkjente();

foreach($oppgaveListe as $oppgave) {
    /* @var \intern3\Oppgave $oppgave */
    $oppgave->arkiver();
}

Epost::sendEpost('data@singsaker.no', '[SING-INTERN]', 'Arkiverte gamle oppgaver, se intern3/ink/arkiver_gamle_oppgaver.php');
exit("Arkiverte gamle oppgaver");