<?php
require_once(__DIR__ . '/../static/topp.php');

$max_invites = 0;
$dager_readable = ['torsdag' => 'Torsdag', 'fredag' => 'Fredag', 'lordag' => 'Lørdag'];
?>
    <style>
        input[type=checkbox] {
            /* Double-sized Checkboxes */
            -ms-transform: scale(2); /* IE */
            -moz-transform: scale(2); /* FF */
            -webkit-transform: scale(2); /* Safari and Chrome */
            -o-transform: scale(2); /* Opera */
            padding: 10px;
        }
    </style>

    <script>
        function fjern(id) {
            $.ajax({
                type: 'POST',
                url: '?a=helga/slett',
                data: 'id=' + id,
                method: 'POST',
                success: function (html) {
                    document.getElementById(id).remove();
                    $('.container').load(document.URL + ' .container');
                },
                error: function (req, stat, err) {
                    alert(err);
                }
            });
        }


        function vis(id) {
            $("#gjest").load("?a=helga/gjestmodal/" + id);
            $("#gjest-modal").modal("show");
        }

        function add(i) {
            var navn = document.getElementById('navn-' + i).value;
            var epost = document.getElementById('epost-' + i).value;

            var days = [];
            ['torsdag-' + i, 'fredag-' + i, 'lordag-' + i].forEach(function (str, index) {
                if (document.getElementById(str).checked) {
                    days.push(str.split('-')[0]);
                }
            });
            var d = days.join(';');

            if (navn.length > 1 && epost.length > 2 && epost.includes('@') && days.length > 0) {
                $.ajax({
                    type: 'POST',
                    url: '?a=helga/add',
                    data: 'navn=' + navn + '&epost=' + epost + '&dager=' + d,
                    method: 'POST',
                    success: function (html) {
                        document.getElementById('add-' + i).remove();
                        $('#lista').load(document.URL + ' #lista');
                        if (html.length > 0) {
                            tilbakemelding(html);
                        }
                    },
                    error: function (req, stat, err) {
                        alert(err);
                    }
                });
            } else {
                tilbakemelding("Ugyldig epost eller ingen dager valgt!");
            }
        }

        function send_epost(id) {
            $.ajax({
                type: 'POST',
                url: '?a=helga/send_epost',
                data: 'id=' + id,
                method: 'POST',
                success: function(html) {
                    tilbakemelding("Sendte epost med invitasjon(er)!");
                }
            })
        }

        function tilbakemelding(beskjed) {
            document.getElementById("success").style.display = "table";
            document.getElementById("tilbakemelding-text").innerHTML = beskjed;
        }

    </script>

    <div class="container">
        <div class="col-lg-12">
            <h1>HELGA » Invitasjon</h1>


            <?php require_once(__DIR__ . '/../static/tilbakemelding.php'); ?>

            <div class="alert alert-success fade in" id="success"
                 style="margin: auto; margin-top: 5%; display:none">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <p id="tilbakemelding-text"></p>
            </div>

            <div class="col-lg-6">

                <p>Her kan du invitere til HELGA!
                    <?php if (!$helga->erSameMax() || $helga->harEgendefinert($beboer->getId())) { ?>
                        <?php $max_invites = $helga->getMaxGjest($beboer->getId(),
                                0) + $helga->getMaxGjest($beboer->getId(), 1) + $helga->getMaxGjest($beboer->getId(),
                                2); ?>
                        Du har <?php echo $helga->getMaxGjest($beboer->getId(), 0); ?> invitasjoner torsdag,
                        <?php echo $helga->getMaxGjest($beboer->getId(), 1); ?> invitasjoner fredag og
                        <?php echo $helga->getMaxGjest($beboer->getId(), 2); ?> invitasjoner lørdag.
                    <?php } else { ?>
                        Du har <?php echo $helga->getMaxAlle(); ?> invitasjoner hver dag.
                        <?php $max_invites = 3 * $helga->getMaxAlle(); ?>
                    <?php }
                      ?>
                </p>
                <p>
                    Invitasjons-status:
                <ul>
                    <li>Torsdag: <?php echo $status['torsdag']; ?>/<?php echo $helga->getMaxGjest($beboer->getId(),
                            0); ?></li>
                    <li>Fredag: <?php echo $status['fredag']; ?>/<?php echo $helga->getMaxGjest($beboer->getId(),
                            1); ?></li>
                    <li>Lørdag: <?php echo $status['lordag']; ?>/<?php echo $helga->getMaxGjest($beboer->getId(),
                            2); ?></li>
                </ul>
                </p>

                <hr>

                <div class="formen">
                    <table class="table table-bordered table-responsive table-condensed">
                        <?php for ($i = 0;
                                   $i < $max_invites - $count;
                                   $i++) { ?>


                            <tr id="add-<?php echo $i; ?>">
                                <td>Navn:
                                    <input id="navn-<?php echo $i; ?>" type="text" name="navn" value=""
                                           class="form-control"/></td>
                                <td>Epost:
                                    <input id="epost-<?php echo $i; ?>" type="text" name="epost" value=""
                                           class="form-control"/></td>
                                <td>
                                    Torsdag <input id="torsdag-<?php echo $i; ?>" class="checkbox-inline"
                                                   type="checkbox" name="torsdag"
                                                   value="torsdag"/>
                                </td>
                                <td>
                                    Fredag <input id="fredag-<?php echo $i; ?>" class="checkbox-inline" type="checkbox"
                                                  name="fredag" value="fredag"/>
                                </td>
                                <td>
                                    Lørdag <input id="lordag-<?php echo $i; ?>" class="checkbox-inline" type="checkbox"
                                                  name="lordag" value="lordag"/>
                                </td>
                                <td>
                                    <button class="btn btn-primary"
                                            onclick="add(<?php echo $i; ?>)">Legg til
                                    </button>
                                </td>
                            </tr>


                        <?php } ?>
                    </table>
                </div>
                <?php
                if ($max_invites - $count < 1) { ?>
                    <p> Det ser ut til at du har brukt opp alle dine invitasjoner. </p>
                <?php }
                  ?>

            </div>

            <div class="col-lg-6" id="lista">

                <h3>Inviterte gjester</h3>

                <p>Her vises de du har invitert til HELGA.</p>

                <hr>

                <button id="torsdag" class="btn btn-primary">Torsdag</button>
                <button id="fredag" class="btn btn-default">Fredag</button>
                <button id="lordag" class="btn btn-info">Lørdag</button>
                <hr>
                <table class="table table-bordered table-responsive table-condensed display compact" id="tabellen">
                    <thead>
                    <th>Navn</th>
                    <th>Epost</th>
                    <th>Torsdag</th>
                    <th>Fredag</th>
                    <th>Lørdag</th>
                    <th>Slett</th>
                    <th>Endre</th>
                    <th>Send Epost</th>
                    </thead>
                    <tbody>
                    <?php foreach ($gjester as $gjest) {
                        /* @var \intern3\HelgaGjesteObjekt $gjest */
                        ?>

                        <tr id="<?php echo $gjest->getIder()[0]; ?>">
                            <td>Navn: <?php echo $gjest->getNavn(); ?></td>
                            <td>Epost: <?php echo $gjest->getEpost(); ?></td>
                            <td id="torsdag-<?php echo $gjest->getIder()[0]; ?>" data-label="<?php echo in_array("0",
                                $gjest->getDager()) ? '1' : '0'; ?>"><?php if (in_array("0", $gjest->getDager())) {
                                    echo "✗";
                                } ?></td>
                            <td id="fredag-<?php echo $gjest->getIder()[0]; ?>" data-label="<?php echo in_array("1",
                                $gjest->getDager()) ? '1' : '0'; ?>"><?php if (in_array("1", $gjest->getDager())) {
                                    echo "✗";
                                } ?></td>
                            <td id="lordag-<?php echo $gjest->getIder()[0]; ?>" data-label="<?php echo in_array("2",
                                $gjest->getDager()) ? '1' : '0'; ?>"><?php if (in_array("2", $gjest->getDager())) {
                                    echo "✗";
                                } ?></td>
                            <td><input class="btn btn-primary" type="submit" value="Slett"
                                       onclick="fjern(<?php echo $gjest->getIder()[0]; ?>)"></td>
                            <td>
                                <button class="btn btn-warning" onclick="vis(<?php echo $gjest->getIder()[0]; ?>)">
                                    Endre
                                </button>
                            </td>
                            <td>
                                <input class="btn btn-info" type="submit" value="Send"
                                       onclick="send_epost(<?php echo $gjest->getIder()[0]; ?>)">
                            </td>
                        </tr>
                        <?php
                    }
                      ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <div class="modal fade" id="gjest-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Endre på gjest</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="gjest">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Lukk</button>
                </div>
            </div>
        </div>
    </div>

    <link rel="stylesheet" type="text/css" href="css/dataTables.css"/>
    <script type="text/javascript" src="js/dataTables.js"></script>


    <script>

        var table;
        $(document).ready(function () {
            var dager = [1, 1, 1];
            var neutral = true;

            table = $('#tabellen').DataTable({
                "paging": false,
                "info": false,
                'destroy': true
            });

            function draw_buttons() {
                if(neutral) {
                    $('#torsdag').text('Torsdag');
                    $('#torsdag').removeClass('btn-danger');
                    $('#torsdag').addClass('btn-primary');

                    $('#fredag').text('Fredag');
                    $('#fredag').removeClass('btn-warning');
                    $('#fredag').addClass('btn-default');

                    $('#lordag').text('Lørdag');
                    $('#lordag').removeClass('btn-success');
                    $('#lordag').addClass('btn-info');

                    return;
                }

                if(dager[0] === 1) {
                    $('#torsdag').text('Ikke Torsdag');
                    $('#torsdag').addClass('btn-danger');
                    $('#torsdag').removeClass('btn-primary');
                } else {
                    $('#torsdag').text('Torsdag');
                    $('#torsdag').removeClass('btn-danger');
                    $('#torsdag').addClass('btn-primary');
                }

                if(dager[1] === 1) {
                    $('#fredag').text('Ikke Fredag');
                    $('#fredag').addClass('btn-warning');
                    $('#fredag').removeClass('btn-default');
                } else {
                    $('#fredag').text('Fredag');
                    $('#fredag').removeClass('btn-warning');
                    $('#fredag').addClass('btn-default');
                }

                if(dager[2] === 1) {
                    $('#lordag').text('Ikke Lørdag');
                    $('#lordag').addClass('btn-success');
                    $('#lordag').removeClass('btn-info');
                } else {
                    $('#lordag').text('Lørdag');
                    $('#lordag').removeClass('btn-success');
                    $('#lordag').addClass('btn-info');
                }

            }

            $.fn.dataTable.ext.search.push(
                function (settings, data, dataIndex) {
                    var arr_sum = dager.reduce((a, b) => a + b, 0);
                    if (arr_sum === 3 || arr_sum === 0) {
                        dager = [1,1,1];
                        neutral = true;

                        draw_buttons();

                        return true;
                    }

                    /*
                    (1 * false || 0 * true || 1 * true) == true => true
                    (1 * false || 1 * false || 1 * false) == true => false
                    (0 * true || 0 * true || 0 * true) == true => false
                    etc.
                    */

                    return  (dager[0] * table.row(dataIndex).node().children[2].innerHTML.length > 0 ||
                        dager[1] * table.row(dataIndex).node().children[3].innerHTML.length > 0 ||
                        dager[2] * table.row(dataIndex).node().children[4].innerHTML.length > 0) == true;
                }
            );

            $('#torsdag').click(function () {

                if(neutral) {
                    neutral = false;
                    dager = [1, 0, 0];
                } else {
                    if (dager[0] === 1) {
                        dager[0] = 0;
                    } else {
                        dager[0] = 1;

                    }
                }

                draw_buttons();
                table.draw();
            });

            $('#fredag').click(function () {

                if(neutral) {
                    neutral = false;
                    dager = [0,1,0];
                } else {

                    if (dager[1] === 1) {
                        dager[1] = 0;
                    } else {
                        dager[1] = 1;
                    }
                }

                draw_buttons();
                table.draw();
            });

            $('#lordag').click(function () {

                if(neutral) {
                    neutral = false;
                    dager = [0,0,1];
                } else {

                    if (dager[2] === 1) {
                        dager[2] = 0;
                    } else {
                        dager[2] = 1;
                    }
                }
                draw_buttons();
                table.draw();
            });

        });

    </script>
    <style>
        .dataTables_filter {
            display: none;
        }
    </style>
<?php

require_once(__DIR__ . '/../static/bunn.php');
