<style>
    body {
        background: url("background.png");
        color: black;
        font-family: sans-serif;
    }

    h2 {
        font-size: 1.2em;
    }

    a {
        color: black;
    }

    p.brannlistested {
        padding: 0 0 0 0;
        margin: 0 0 0 0;
    }

    th, td {
        border-right: 1px solid #000000;
        border-bottom: 1px solid #000000;
        font-size: 80%;
        text-align: left;
    }

    .header {
        background: #993333;
        color: white;
        padding: 4px;
    }
    .header img {
        float: left;
    }
    .header h1 {
        font-size: 1.4em;
        text-align: right;
        margin: 0px;
        padding: 0px;
    }

    .menu_sections {
        background: #c0c0c0;
        padding: 4px 0px;
    }
    .menu_sections a {
        padding: 0px 4px;
        text-decoration: none;
    }
    .menu_sections a:hover {
        text-decoration: underline;
    }

    .menu_subsections {
        background: #e0e0e0;
        padding: 4px 0px;
    }
    .menu_subsections a {
        font-weight: normal;
        padding: 0px 4px;
        text-decoration: none;
    }
    .menu_subsections a:hover {
        text-decoration: underline;
    }

    .menu_selected {
        background: #e0e0e0;
        font-weight: bold;
        padding: 4px;
    }

    .footer {
        background: #c0c0c0;
        color: black;
        font-size: smaller;
        padding: 4px;
    }

    .ok {
        background: transparent;
        color: #55ff34;
    }

    .error {
        background: transparent;
        color: #aa2245;
        font-weight: bold;
    }

    .slettet {
        background: transparent;
        color: #ffee33;
    }

    .table_header {
        background: #c0c0c0;
        color: black;
    }

    #apmandliste {
        border: 0 0 0 0;
        width: 100%;
    }

    #apmandliste th {
        border-right: 0;
    }

    #apmandliste td {
        border-bottom: 0;
        border-right: 0;
    }

    #apmandliste td.svart {
        border-right: 1px solid #000000;
        border-bottom: 0;
    }

    #beboerlistetop {
        border-bottom: 3px solid #000000;
        width: 100%;
    }

    #beboerlistetop th.left {
        border-right: 0;
        border-bottom: 0;
        color: black;
        font-size: 125%;
        text-align: left;
        vertical-align: bottom;
        width: 30%;
    }

    #beboerlistetop th.center {
        border-right: 0;
        border-bottom: 0;
        color: black;
        font-size: 150%;
        text-align: center;
        vertical-align: bottom;
        width: 40%;
    }

    #beboerlistetop th.right {
        border-right: 0;
        border-bottom: 0;
        color: black;
        font-size: 100%;
        text-align: right;
        vertical-align: bottom;
        width: 30%;
    }

    #beboerliste {
        border-bottom: 3px solid #000000;
        width: 100%;
    }

    #beboerliste a {
        text-decoration: none;
    }

    #beboerliste a:hover {
        text-decoration: underline;
    }

    #beboerliste th.heading {
        border-bottom: 2px solid #000000;
        border-right: 0;
    }

    #beboerliste td.data {
        border-bottom: 0;
        border-right: 0;
        font-size: 64%;
    }

    #beboerliste td.data_left {
        border-bottom: 0;
        border-right: 0;
        border-bottom: 1px solid #000000;

    }

    #beboerliste td.data_center {
        border-bottom: 0;
        border-right: 0;
        text-align: center;
        border-bottom: 1px solid #000000;

    }

    #brannlistedata {
        width: 100%;
    }

    #brannlistedata th {
        border-bottom: 0;
        border-right: 0;
    }

    #brannlistedata th.kryss {
        width: 12px;
    }

    #brannlistedata td {
        border-bottom: 0;
        border-right: 0;
    }

    #brannlistedata td.kryss {
        border: 2px solid;
        width: 12px;
    }

    #brannlistedata td.rom {
        text-align: center;
    }

    #brannlistedata td.brannoppgave {
        width: 25%;
    }

    #brannlistetop {
        width: 100%;
    }

    #brannlistetop th.tittel {
        border-bottom: 0;
        border-right: 0;
        font-size: 115%;
        text-align: left;
        padding-left: 5%;
        padding-right: 5%;
    }

    #brannlistetop th.dato {
        border-bottom: 0;
        border-right: 0;
        text-align: right;
    }

    #brannlistetop td {
        border-bottom: 0;
        border-right: 0;
        font-style: italic;
        text-align: center;
    }

    #brannlistetop tr.fetlinje {
        border-bottom: 2px;
    }

    #brannlistefetlinje {
        border-style: solid;
        border-width: 2px;
    }

    #krysseliste {
        border-spacing: 0;
        width: 100%;
    }

    #krysseliste th {
        border-bottom: 2px solid;
        border-right: 0;
    }

    #krysseliste th.navn {
        text-align: center;
    }

    #krysseliste th.antall {
        text-align: center;
        width: 15%;
    }

    #krysseliste th.sum {
        text-align: right;
    }

    #krysseliste td {
        border-bottom: 1px solid;
        border-right: 1px solid;
    }

    #krysseliste td.navn {
        font-size: 64%;
    }

    #krysseliste td.antall {
        border-right: 0;
    }

    #krysseliste td.sum {
        border: 1px solid;
        border-top: 0;
    }

    #krysseliste td.tom {
        border: 0 solid;
    }

    #krysselistebunn {
        border-spacing: 0;
        width: 50%;
    }

    #krysselistebunn td.tom {
        border: 0 solid;
    }

    #krysselistebunn td.top {
        border-bottom: 2px solid;
        border-right: 0;
    }

    #krysselistebunn td.midt {
        border-bottom: 3px solid;
        border-right: 0;
        height: 150%;
    }

    #krysselistebunn td.bunn {
        border-bottom: 1px solid;
        border-right: 0;
    }

    #krysselistetop {
        width: 100%;
        border-spacing: 0;
    }

    #krysselistetop th {
        border-bottom: 3px solid;
        border-right: 0;
    }

    #krysselistetop th.tittel {
    }

    #krysselistetop th.dato {
        text-align: right;
    }

    #nyttigenummer {
        border-bottom: 0;
        border-right: 0;
        width: 100%;
    }

    #nyttigenummer td {
        border-bottom: 0;
        border-right: 0;
    }

    #nyttigenummer td.antbeboere {
        border-bottom: 0;
        border-right: 0;
        text-align: right;
    }

    #vaktliste {
        width: 100%;
    }

    #vaktliste td {
        height: 5em;
        text-align: left;
        vertical-align: bottom;
    }

    #vaktliste th {
        text-align: center;
    }

    #vaktliste th.dag {
        width: 10%;
    }

    #vaktliste th.dato {
        width: 10%;
    }

    #vaktliste th.vakt {
        width: 20%;
    }

    #vaktliste tr {
    }
    #kryssing{
        margin-left: auto;
        margin-right: auto;
        border-width: 2px 2px 2px 2px;
        border-spacing: 1px;
        border-style: dotted dotted dotted dotted;
        border-collapse: collapse;
        background-color: #DFD;
    }
    #kryssing td.tall {
        font-size: 40px;
        font-style: bold;
        align: center;
        border-width: medium medium medium medium;
        padding: 5px 5px 5px 5px;
        border-style: dotted dotted dotted dotted;
        border-color: gray gray gray gray;
        border-collapse: collapse;
        background-color: rgb(250, 240, 230);
        /*      -moz-border-radius: 3px 3px 3px 3px; */
    }
    #ol{
        width: 100%;
        margin-right: 0px;
        margin-left: 0px;
        text-align: center;
        background-color: #3C6;
    }
    #cider{
        width: 100%;
        margin-right: 0px;
        margin-left: 0px;
        text-align: center;
        background-color: #FC0;
    }

    .godkjenn_alle td tr{
        border: 0px solid #000000;
    }

</style>
    <div class="col-md-12">
        <table id="beboerlistetop">
            <tr>
                <th class="left">Beboerliste</th>
                <th class="center">Singsaker Studenterhjem</th>
                <th class="right">Utskriftsdato: <?php echo date('Y-m-d');?></th>
            </tr>
        </table>
        <table class="table table-bordered table-responsive" id="beboerliste">
            <tr>
                <th class="heading">Navn</th>
                <th class="heading">Rom</th>
                <th class="heading">Telefon</th>
                <th class="heading">Epost</th>
                <th class="heading">Studie</th>
                <th class="heading">Født</th>
                <th class="heading">VIP</th>
            </tr>
            <?php

            foreach ($beboerListe as $beboer){
                ?>
                <tr>
                    <td class="data"><?php echo $beboer->getFulltNavn(); ?></td>
                    <td class="data"><?php echo $beboer->getRom()->getNavn(); ?></td>
                    <td class="data"><?php echo $beboer->getTelefon(); ?></td>
                    <td class="data"><?php echo $beboer->getEpost(); ?></td>
                    <td class="data"><?php
                        $studie = $beboer->getStudie();
                        $skole = $beboer->getSkole();
                        if ($studie == null || $skole == null) {
                            echo ' ';
                        }
                        else {
                            echo $beboer->getKlassetrinn();
                            ?>. <?php echo $studie->getNavn(); ?>&nbsp;(<?php echo $skole->getNavn(); ?>)<?php
                        }
                        ?></td>
                    <td class="data"><?php echo $beboer->getFodselsdato(); ?></td>
                    <td class="data"><?php
                        $utvalgVervListe = $beboer->getUtvalgVervListe();
                        if (count($utvalgVervListe) == 0) {
                            //echo str_replace(' ', '&nbsp;', $beboer->getRolle()->getNavn());
                        }
                        else {
                            echo '<strong>' . $utvalgVervListe[0]->getNavn() . '</strong>';
                        }
                        ?></td>
                </tr>
                <?php
            }

            ?>
        </table>
    </div>
<?php
require_once('bunn.php');
?>