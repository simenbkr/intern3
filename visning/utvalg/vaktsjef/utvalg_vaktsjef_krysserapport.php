<?php

require_once(__DIR__ . '/../topp_utvalg.php');


?>
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
            var dato = document.getElementById('datoen').value;
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

        $('form > input').keyup(function () {
            console.log("asdkasdjasdghjas");
            var empty = false;
            $('form > input[required]').each(function () {
                if ($(this).val() == '') {
                    empty = true;
                }
            });

            if (empty) {
                $('#knappen').attr('disabled', 'disabled');
            } else {
                $('#knappen').removeAttr('disabled');
            }
        });

        $("body").on('keyup', '#datoen', datothingy);
        $("body").on('click', '#datoen', datothingy);
        $("body").on('change', '#datoen', datothingy);


        function datothingy() {
            var empty = false;

            if (document.getElementById('datoen').value.length > 0) {
                empty = true;
            } else {
                empty = false;
            }


            if (empty) {
                $('#knappen').attr('disabled', 'disabled');
            } else {
                $('#knappen').removeAttr('disabled');
            }
        }

        $(function () {
            $('#datoen').datepicker({
                dateFormat: 'yy-mm-dd',
                onSelect: function (datetext) {
                    var d = new Date(); // for now
                    var h = d.getHours();
                    h = (h < 10) ? ("0" + h) : h;

                    var m = d.getMinutes();
                    m = (m < 10) ? ("0" + m) : m;

                    var s = d.getSeconds();
                    s = (s < 10) ? ("0" + s) : s;

                    datetext = datetext + " " + h + ":" + m;
                    $('#datoen').val(datetext);
                },
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

        <?php include(__DIR__ . '/../../static/tilbakemelding.php'); ?>

        <table class="table table-bordered table-responsive">
            <tr>
                <th class="tittel">Krysseliste</th>
                <th class="dato">
                    Fra: <?php echo date('Y-m-d H:i', strtotime($sistFakturert)); ?>
                    Til: <?php echo date('Y-m-d H:i'); ?>
                </th>
            </tr>
        </table>
        <?php /* <input class="btn btn-primary" type="submit" value="Nullstill" onclick="sett_fakturert()"> */ ?>
        <h4>Sett alle til fakturert: (dette kan ta opp til 10s)</h4> (Ingen vei tilbake etter at du har trykket!)
        <p>
            Fakturer til NÅ:
            <input type="button" class="btn btn-md btn-danger" value="Nullstill" data-toggle="modal"
                   onclick="get_periode_csv()"
                   data-target="#modal-nullstill">

            Fakturer til spesifikt nullpunkt:
            <input type="button" class="btn btn-md btn-warning" value="Nullstill" data-toggle="modal"
                   data-target="#modal-nullstill2">
        </p>

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
                        <p>Fyll inn dato:
                        <form><input class="form-control" id="datoen" type="text" required/></form>
                        </p>
                        <button type="button" class="btn btn-md btn-danger" id="knappen" onclick="sett_fakturert_dato()"
                                disabled="disabled">Ja!
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
?>