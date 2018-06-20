<?php

require_once(__DIR__ . '/../topp_utvalg.php');

?>

    <div class="col-md-12">
        <h1>Utvalget &raquo; Sekretær &raquo; Lister</h1>
        <p></p>
        <h2>Skrive ut lister</h2>

        <a href="?a=beboer/utskrift">Beboerliste utskriftsvennlig</a><br/>
        <a href="?a=utvalg/sekretar/lister/apmandsverv">Åpmandsverv</a><br/>
        <a href="?a=utvalg/sekretar/lister/apmandsverv_utskrift">Åpmandsverv utskriftsvennlig</a><br/>
        <a href="?a=utvalg/sekretar/lister/apmandsverv_beskrivelser">Åpmandsverv med beskrivelse</a><br/>
        <a href="?a=utvalg/sekretar/lister/apmandsverv_beskrivelser_utskrift">Åpmandsverv med beskrivelse (utskrift)</a><br/>
    </div>

<?php
require_once(__DIR__ . '/../../static/bunn.php');
?>