<?php

require_once(__DIR__ . '/../topp_utvalg.php');


?>
    <script src="js/moment.min.js"></script>
    <script src="js/bootstrap-datetimepicker.min.js"></script>

    <script>
        function sett_fakturert() {
            $("#kult").show();
            $.ajax({
                type: 'POST',
                url: '?a=utvalg/vaktsjef/krysserapport',
                data: 'settfakturert=1',
                method: 'POST',
                success: function (html) {
                    location.reload();
                },
                error: function (req, stat, err) {
                    alert(err);
                }
            });
        }

        function get_periode_csv() {
            var win = window.open('?a=utvalg/vaktsjef/krysserapport_csv', '_blank');
            win.focus();
        }

        function sett_fakturert_dato() {
            var dato = document.getElementById('datoen-tekst').value;
            $("#kult2").show();
            $.ajax({
                type: 'POST',
                url: '?a=utvalg/vaktsjef/krysserapport',
                data: 'settfakturert=2&dato=' + dato,
                method: 'POST',
                success: function (html) {
                    location.reload();
                },
                error: function (req, stat, err) {
                    alert(err);
                }
            });
        }

        $("body").on('keyup', '#datoen', datothingy);
        $("body").on('click', '#datoen', datothingy);
        $("body").on('change', '#datoen', datothingy);


        function datothingy() {
            var empty = true;

            if (document.getElementById('datoen-tekst').value.length > 0) {
                empty = false;
            } else {
                empty = true;
            }


            if (empty) {
                $('#knappen').attr('disabled', 'disabled');
            } else {
                $('#knappen').removeAttr('disabled');
            }
        }

        function vis_utskrift() {
            var dato = document.getElementById('dato-utskrift-tekst').value;
            window.location = '?a=utvalg/vaktsjef/krysserapportutskrift/' + dato;
        }

        $(function(){
            $('#datoen').datetimepicker({
               format: 'YYYY-MM-DD HH:mm:ss'
            });
        });


        $(function () {
            $('#dato-utskrift').datetimepicker({
                format: 'YYYY-MM-DD HH:mm:ss'
            });
        });


        $(function () {
            $("#datepicker").datepicker({dateFormat: "yy-mm-dd"});
        });

    </script>
    <div class="container">
        <h1>Utvalget » Vaktsjef » Krysserapport</h1>
        <h3>[ Krysserapport ] [ <a
                    href="<?php echo $cd->getBase(); ?>utvalg/vaktsjef/krysserapportutskrift">Utskrift</a> ]

        </h3>

        <hr>

        <input type="button" class="btn btn-default" data-toggle="modal" data-target="#modal-utskrift" value="Utskrift
            fra <?php echo $sistFakturert; ?> til bestemt tidspunkt"/>

        <hr>

        <?php require_once(__DIR__ . '/../../static/tilbakemelding.php'); ?>

        <table class="table table-bordered table-responsive">
            <tr>
                <th class="tittel">Krysseliste</th>
                <th class="dato">
                    Fra: <?php echo date('Y-m-d H:i:s', strtotime($sistFakturert)); ?>
                    Til: <?php echo date('Y-m-d H:i:s'); ?>
                </th>
            </tr>
        </table>
        <h4>Sett alle til fakturert: (dette kan ta lang tid..)</h4> (Ingen vei tilbake etter at du har trykket!)
        <p>
            Fakturer til NÅ:
            <input type="button" class="btn btn-md btn-danger" value="Nullstill" data-toggle="modal"
                   onclick="get_periode_csv()"
                   data-target="#modal-nullstill">

            Fakturer til spesifikt nullpunkt:
            <input type="button" class="btn btn-md btn-warning" value="Nullstill" data-toggle="modal"
                   data-target="#modal-nullstill2">
        </p>

        <p>For hver periode som skrives ut til Torild, er det essensielt at man markerer periodens kryss som
            "fakturert".
            Det gjøres primært gjennom den RØDE knappen over, men i spesielle tilfeller (les: der fakturering har blitt
            glemt)
            kan den GULE knappen benyttes. Det er svært viktig at det IKKE krysses mens fakturering pågår.</p>

        <div class="modal fade" id="modal-nullstill" role="dialog">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Ønsker du å nullstille kryssetabellen? Husket å skrive ut?</h4>
                    </div>
                    <div class="modal-body">
                        <button type="button" class="btn btn-md bform > tn-danger" onclick="sett_fakturert()">Ja!
                        </button>
                        <div id="kult" style="display:none">
                            <p>
                                Fakturer nå. Chillen litt'a, plis.

                                <img src="beboerkart/loading.gif">
                            </p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Lukk</button>
                    </div>
                </div>
            </div>
        </div>


        <div class="modal fade" id="modal-nullstill2" role="dialog">


            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Ønsker du å nullstille kryssetabellen? Husket å skrive ut?</h4>
                    </div>
                    <div class="modal-body">
                        <p>Fyll inn dato:</p>
                        <div class="form-group">
                            <div class='input-group date' id='datoen'>
                                <input id="datoen-tekst" type='text' class="form-control"/>
                                <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                            </div>
                        </div>
                        <button type="button" class="btn btn-md btn-danger" id="knappen" onclick="sett_fakturert_dato()" disabled="disabled">Ja!
                        </button>
                        <div id="kult2" style="display:none">
                            <p>
                                Fakturer nå. Chillen litt'a, plis.

                                <img src="beboerkart/loading.gif">
                            </p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Lukk</button>
                    </div>
                </div>
            </div>
        </div>


        <div class="modal fade" id="modal-utskrift" role="dialog">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Velg dato</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <div class='input-group date' id='dato-utskrift'>
                                <input id="dato-utskrift-tekst" type='text' class="form-control"/>
                                <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                            </div>
                        </div>
                        <button class="btn btn-warning" onclick="vis_utskrift()">Vis</button>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Lukk</button>
                    </div>
                </div>
            </div>
        </div>


        <br/>
        <table class="table table-bordered table-responsive">

            <tr>
                <th class="">Navn</th>
                <?php

                /* @var \intern3\Drikke $drikken */

                $sum = array();
                foreach ($drikke as $drikken) {
                    /*
                    if($drikken->getId() == 1 || $drikken->getNavn() == 'Pant'){
                        continue;
                    } */
                    if (//($drikken->getId() == 1 || $drikken->getNavn() == 'Pant' ||
                    (!$drikken->harBlittDrukketSiden($sistFakturert) && $drikken->getAktiv() == 0)
                    ) {
                        continue;
                    }
                    ?>
                    <th class=""><?php echo $drikken->getNavn(); ?></th>
                    <?php

                    $sum[$drikken->getNavn()] = 0;
                }
                ?>
            </tr>
            <?php foreach ($krysseListeMonthListe as $beboerID => $krysseliste) {
                $beboeren = $beboerListe[$beboerID]; ?>

                <tr>
                    <td class="navn"><a
                                href="?a=utvalg/vaktsjef/detaljkryss/<?php echo $beboeren->getId(); ?>"><?php echo $beboeren->getFulltNavn(); ?>
                    </td>
                    <?php foreach ($drikke as $drikken) {
                        /*
                        if($drikken->getId() == 1 || $drikken->getNavn() == 'Pant' || $drikken->harBlittDrukketSiden($sistFakturert)){
                            continue;
                        } */
                        if (//($drikken->getId() == 1 || $drikken->getNavn() == 'Pant' ||
                        (!$drikken->harBlittDrukketSiden($sistFakturert) && $drikken->getAktiv() == 0)
                        ) {
                            continue;
                        }

                        $sum[$drikken->getNavn()] += $krysseliste[$drikken->getNavn()];
                        ?>
                        <td class="<?php echo $drikken->getNavn(); ?>"><?php echo $krysseliste[$drikken->getNavn()]; ?></td>
                    <?php } ?>
                </tr>

                <?php
            }
            ?>
            <tr>
                <td>TOTALT</td>
                <?php foreach ($sum as $drikken => $verdi) { ?>
                    <td><?php echo $verdi; ?></td>
                    <?php
                }
                ?>

            </tr>
        </table>
        <?php
        /*<tr>
            <th class="">Navn</th>
            <th class="">Øl</th>
            <th class="">Cider</th>
            <th class="">Carlsberg</th>
            <th class="">Rikdom</th>
            <th class="">Pant</th>
        </tr>
        <?php
        foreach ($krysseListeMonthListe as $beboerID => $krysseliste) {
            $beboeren = $beboerListe[$beboerID];
            ?>
            <tr>
                <td class="navn"><a
                        href="?a=utvalg/vaktsjef/detaljkryss/<?php echo $beboeren->getId(); ?>"><?php echo $beboeren->getFulltNavn(); ?></a>
                </td>
                <td class="øl"><?php echo $krysseliste['Øl']; ?></td>
                <td class="cider"><?php echo $krysseliste['Cider'] ?></td>
                <td class="carlsberg"><?php echo $krysseliste['Carlsberg']; ?></td>
                <td class="rikdom"><?php echo $krysseliste['Rikdom']; ?></td>
                <td class="pant"><?php echo $krysseliste['Pant']; ?></td>
            </tr>
            <?php
        }
        ?>
    </table> */ ?>


    </div>

<?php
require_once(__DIR__ . '/../../static/bunn.php');