<?php

namespace intern3;

require_once(__DIR__ . '/../../ink/autolast.php');
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="utf-8">
    <title>Intern3.0</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/stilark.css">
    <script src="js/jquery-2.1.4.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery-ui.js"></script>
    <link rel="stylesheet" href="css/jquery-ui.css">
    <script>
        var formaterDatovelger = function () {
            $('.datepicker').datepicker({dateFormat: "yy-mm-dd"});
        };
        $(formaterDatovelger);
    </script>
</head>
<body>
<div id="ramme">


    <?php
    if (!isset($skjulMeny)) {

    if ($_SERVER['SERVER_NAME'] != 'intern.singsaker.no' && $_SERVER['SERVER_NAME'] != 'dobbel.singsaker.no') {
        echo "Dette er en utviklingsside. Den er koblet til følgende database: ";
        print_r(DB::getDB()->query('SELECT database()')->fetchColumn());
        echo "<br/>";
        var_dump($_SESSION);
    }
    /* Meny start */
    ?>

    <nav class="navbar navbar-default" id="topp">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                        data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="?a=diverse">intern.singsaker.no</a>
            </div>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <?php if (Storhybelliste::finnesAktive()) { ?>
                        <li><a href="<?php echo $cd->getBase(); ?>storhybel">Storhybel</a></li>
                    <?php } ?>
                    <li><a href="<?php echo $cd->getBase(); ?>beboer">Beboer</a></li>
                    <li><a href="<?php echo $cd->getBase(); ?>vakt">Vakt</a></li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                           aria-expanded="false">Regi <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="<?php echo $cd->getBase(); ?>regi/oppgave">Oppgaver</a></li>
                            <!--<li><a href="<?php echo $cd->getBase(); ?>regi/rapport">Rapporter</a></li>-->
                            <li><a href="<?php echo $cd->getBase(); ?>regi/minregi">Min regi</a></li>
                            <li><a href="<?php echo $cd->getBase(); ?>regi/regivakt">Regivakt</a></li>
                            <?php /*<li><a href="<?php echo $cd->getBase(); ?>regi/registatus">Registatus</a></li>*/ ?>
                        </ul>
                    </li>
                    <li><a href="<?php echo $cd->getBase(); ?>verv">Verv</a></li>
                    <li><a href="<?php echo $cd->getBase(); ?>kryss">Kryss</a></li>
                    <li><a href="<?php echo $cd->getBase(); ?>wiki">Wiki</a></li>
                    <!--<li><a href="<?php echo $cd->getBase(); ?>utleie">Utleie</a></li>-->


                    <?php if($cd->getAktivBruker() != null && $cd->getAktivBruker()->getPerson()->erJubileumsAnsvarlig()) { ?>
                        <li><a href="<?php echo $cd->getBase(); ?>jubileum">Jubileum</a></li>

                    <?php
                    } ?>

                    <?php
                    //if ($cd->getAktivBruker() != null && $cd->getAktivBruker()->getPerson()->erHelgaGeneral()) {
                    if ($_SESSION['helga']) {
                        ?>
                        <ul class="nav navbar-nav">
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                                   aria-haspopup="true" aria-expanded="false">Helga <span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li><a href="<?php echo $cd->getBase(); ?>helga">Helga</a></li>
                                    <li><a href="<?php echo $cd->getBase(); ?>helga/general">General</a></li>
                                    <li><a href="<?php echo $cd->getBase(); ?>helga/inngang">Inngang</a></li>
                                </ul>
                            </li>
                        </ul>
                        <?php
                    } elseif (Helga::getLatestHelga() != null && Helga::getLatestHelga()->getKlar()
                        && $cd->getAktivBruker() != null && $cd->getAktivBruker()->getPerson()->harHelgaTilgang()) {
                        ?>
                        <ul class="nav navbar-nav">
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                                   aria-haspopup="true" aria-expanded="false">Helga <span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li><a href="<?php echo $cd->getBase(); ?>helga">Helga</a></li>
                                    <li><a href="<?php echo $cd->getBase(); ?>helga/inngang">Inngang</a></li>
                                </ul>
                            </li>
                        </ul>
                        <?php
                    } elseif (Helga::getLatestHelga() != null && Helga::getLatestHelga()->getKlar()) {
                        ?>
                        <li><a href="<?php echo $cd->getBase(); ?>helga">Helga</a></li>
                    <?php } ?>
                    <?php if ($_SESSION['kjellermester'] || $_SESSION['data']) { ?>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                               aria-haspopup="true" aria-expanded="false">Kjellermester <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo $cd->getBase(); ?>kjeller/admin">Vinadministrasjon</a></li>
                                <li><a href="<?php echo $cd->getBase(); ?>kjeller/leggtil">Legg til vin</a></li>
                                <li><a href="<?php echo $cd->getBase(); ?>kjeller/add_type">Legg til/endre vintyper</a>
                                </li>
                                <li><a href="<?php echo $cd->getBase(); ?>kjeller/pafyll">Påfyll</a></li>
                                <li><a href="<?php echo $cd->getBase(); ?>kjeller/lister">Lister</a></li>
                                <li><a href="<?php echo $cd->getBase(); ?>kjeller/regning">Registrer regning</a></li>
                                <li><a href="<?php echo $cd->getBase(); ?>kjeller/svinn">Registrer svinn</a></li>
                                <li><a href="<?php echo $cd->getBase(); ?>kjeller/oversikt">Oversikt</a></li>
                                <li><a href="<?php echo $cd->getBase(); ?>kjeller/regler">Regler</a></li>
                                <li><a href="<?php echo $cd->getBase(); ?>vinkjeller/">Vinkjelleren</a></li>
                            </ul>
                        </li>
                        <?php
                    }
                    if ($_SESSION['utvalg']) {
                        ?>
                        <li><a href="<?php echo $cd->getBase(); ?>utvalg">Utvalget</a></li>
                        <?php
                    }

                    ?>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                           aria-expanded="false"><?php echo $cd->getAktivBruker()->getPerson()->getFulltNavn(); ?> <span
                                    class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="<?php echo $cd->getBase(); ?>profil">Profil</a></li>
                            <?php

                            if ($cd->getAktivBruker()->getPerson()->erBeboer()) {
                                ?>
                                <li><a href="<?php echo $cd->getBase(); ?>romskjema">Romskjema
                                        (<?php echo $cd->getAktivBruker()->getPerson()->getRomhistorikk()->getAktivtRom()->getNavn(); ?>
                                        )</a></li>
                                <?php /*<li><a href="<?php echo $cd->getBase(); ?>rombytte">Rombytte</a></li> */ ?>
                                <li><a href="<?php echo $cd->getBase(); ?>utflytting">Utflytting</a></li>
                                <?php
                            }

                            ?>
                            <li role="separator" class="divider"></li>
                            <li><a href="<?php

                                if ($cd->getAdminBruker() == null) {
                                    echo $cd->getBase() . 'logginn/loggut&amp;ref=' . htmlspecialchars($_SERVER['REQUEST_URI']);
                                } else {
                                    echo substr($cd->getBase(), 0, strrpos(rtrim($cd->getBase(), '/'), '/'));
                                }

                                ?>">Logg ut</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
        <?php
        /* Meny slutt */
        }
        ?>

        <?php
        if (isset($visUtvalgMeny)) {
            /* Utvalgmeny start */
            ?>
            <div class="container-fluid">
                <div class="navbar-header">
                    <a class="navbar-brand" href="?a=utvalg">Utvalget</a>
                    <ul class="nav navbar-nav">
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                               aria-haspopup="true" aria-expanded="false">Romsjef <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo $cd->getBase(); ?>utvalg/romsjef/beboerliste">Beboerliste</a>
                                </li>
                                <li><a href="<?php echo $cd->getBase(); ?>utvalg/romsjef/gammelbeboer_tabell">Gamle
                                        beboerliste</a></li>
                                <li><a href="<?php echo $cd->getBase(); ?>utvalg/romsjef/nybeboer">Legg til ny
                                        beboer</a></li>
                                <li><a href="<?php echo $cd->getBase(); ?>utvalg/romsjef/epost">E-postlister</a></li>
                                <li><a href="<?php echo $cd->getBase(); ?>utvalg/romsjef/veteran">Veteranliste</a></li>
                                <li><a href="<?php echo $cd->getBase(); ?>utvalg/romsjef/ansiennitet">Ansiennitet</a>
                                </li>
                                <li>
                                    <a href="<?php echo $cd->getBase(); ?>utvalg/romsjef/storhybel/liste">Storhybelliste</a>
                                </li>
                                <li><a href="<?php echo $cd->getBase(); ?>utvalg/romsjef/soknad">Søknader</a></li>
                                <?php /*<li><a href="<?php echo $cd->getBase(); ?>utvalg/romsjef/endrebeboer">Endre beboer</a></li>
                    <li><a href="<?php echo $cd->getBase(); ?>utvalg/romsjef/endregammelbeboer">Endre Gammel beboer</a></li>*/ ?>
                            </ul>
                        </li>
                    </ul>
                    <ul class="nav navbar-nav">
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                               aria-haspopup="true" aria-expanded="false">Regisjef <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo $cd->getBase(); ?>utvalg/regisjef/regivakt">Regivakt</a></li>
                                <li><a href="<?php echo $cd->getBase(); ?>utvalg/regisjef/arbeid">Loggført Arbeid</a>
                                </li>
                                <li><a href="<?php echo $cd->getBase(); ?>utvalg/regisjef/oppgave">Tildelig av
                                        Oppgaver</a></li>
                                <li><a href="<?php echo $cd->getBase(); ?>utvalg/regisjef/registatus">Registatus</a>
                                </li>
                                <li><a href="<?php echo $cd->getBase(); ?>utvalg/regisjef/regiliste">Regilister</a></li>
                                <li><a href="<?php echo $cd->getBase(); ?>utvalg/regisjef/leggtilarbeid">Legg til
                                        arbeid</a></li>
                                <li><a href="<?php echo $cd->getBase(); ?>utvalg/fordeling">Rollefordeling</a></li>
                            </ul>
                        </li>
                    </ul>

                    <ul class="nav navbar-nav">
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                               aria-haspopup="true" aria-expanded="false">Sekretær <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo $cd->getBase(); ?>utvalg/sekretar/apmandsverv">Åpmandsverv</a>
                                </li>
                                <!-- Redundant<li><a href="<?php echo $cd->getBase(); ?>utvalg/sekretar/apmandstimer">Endre regitimer for verv</a></li> -->
                                <li><a href="<?php echo $cd->getBase(); ?>utvalg/sekretar/utvalgsverv">Utvalgsverv</a>
                                </li>
                                <li><a href="<?php echo $cd->getBase(); ?>utvalg/sekretar/helga">Helga</a></li>
                                <li><a href="<?php echo $cd->getBase(); ?>utvalg/sekretar/lister">Skrive ut lister</a>
                                </li>
                            </ul>
                        </li>
                    </ul>

                    <ul class="nav navbar-nav">
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                               aria-haspopup="true" aria-expanded="false">Vaktsjef <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo $cd->getBase(); ?>utvalg/vaktsjef/vaktstyring">Vaktstyring</a>
                                </li>
                                <li><a href="<?php echo $cd->getBase(); ?>utvalg/vaktsjef/vaktoversikt">Vaktoversikt</a>
                                </li>
                                <li><a href="<?php echo $cd->getBase(); ?>utvalg/vaktsjef/generer">Generer vaktliste</a>
                                </li>
                                <li><a href="<?php echo $cd->getBase(); ?>utvalg/vaktsjef/ukerapport">Ukerapport</a>
                                </li>
                                <li>
                                    <a href="<?php echo $cd->getBase(); ?>utvalg/vaktsjef/krysserapport">Krysserapport</a>
                                </li>
                                <li><a href="<?php echo $cd->getBase(); ?>utvalg/vaktsjef/drikke">Drikke</a></li>
                                <li><a href="<?php echo $cd->getBase(); ?>utvalg/vaktsjef/vaktliste_utskrift">Vaktliste
                                        Utskriftsvennlig</a></li>
                                <li><a href="<?php echo $cd->getBase(); ?>utvalg/vaktsjef/krysserapport_historie">Krysserapport
                                        Historie</a></li>
                                <li><a href="<?php echo $cd->getBase(); ?>utvalg/vaktsjef/kryss">Kryss minutt for
                                        minutt</a></li>
                                <li><a href="<?php echo $cd->getBase(); ?>utvalg/fordeling">Rollefordeling</a></li>
                            </ul>
                        </li>
                    </ul>

                    <ul class="nav navbar-nav">
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                               aria-haspopup="true" aria-expanded="false">Kosesjef <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo $cd->getBase(); ?>utvalg/kosesjef/utleie">Utleie</a></li>
                                <li><a href="<?php echo $cd->getBase(); ?>utvalg/kosesjef/krysseliste">Krysseliste for
                                        Bodega</a></li>
                            </ul>
                        </li>
                    </ul>

                    <ul class="nav navbar-nav">
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                               aria-haspopup="true" aria-expanded="false">Husfar <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo $cd->getBase(); ?>utvalg/husfar/epost">Rådets E-postlister</a></li>
                            </ul>
                        </li>
                    </ul>

                    <ul class="nav navbar-nav">
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                               aria-haspopup="true" aria-expanded="false">Resepsjonen <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo $cd->getBase(); ?>journal/hoved">Journal</a></li>
                            </ul>
                        </li>
                    </ul>

                    <ul class="nav navbar-nav">
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                               aria-haspopup="true" aria-expanded="false">Vinkjelleren <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo $cd->getBase(); ?>vinkjeller/">Vinkjelleren</a></li>
                            </ul>
                        </li>
                    </ul>

                </div>
            </div>

            <?php
            /* Utvalgmeny slutt */
        }
        ?>
    </nav>

    <div id="innhold">

        <!-- innhold -->
