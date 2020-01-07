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

        function tilbakemelding(beskjed) {
            document.getElementById("success").style.display = "table";
            document.getElementById("tilbakemelding-text").innerHTML = beskjed;
        }

    </script>

    <div class="container">
        <div class="col-lg-12">
            <h1>HELGA » Invitasjon</h1>


            <?php require_once(__DIR__ . '/../static/tilbakemelding.php'); ?>

            <div class="alert alert-danger fade in" id="success"
                 style="margin: auto; margin-top: 5%; display:none">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <p id="tilbakemelding-text"></p>
            </div>

            <div class="col-lg-7">

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


                <?php for ($i = 0; $i < $max_invites - $count; $i++) { ?>
                    <div class="formen">
                        <table class="table table-bordered table-responsive">
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
                        </table>
                    </div>

                <?php }
                if ($max_invites - $count < 1) { ?>
                    <p> Det ser ut til at du har brukt opp alle dine invitasjoner. </p>
                <?php }
                  ?>

            </div>

            <div class="col-lg-5" id="lista">

                <button id="torsdag" class="btn btn-primary">Torsdag</button>
                <button id="fredag" class="btn btn-default">Fredag</button>
                <button id="lordag" class="btn btn-info">Lørdag</button>
                <hr>
                <table class="table table-bordered table-responsive" id="tabellen">
                    <thead>
                    <th>Navn</th>
                    <th>Epost</th>
                    <th>Torsdag</th>
                    <th>Fredag</th>
                    <th>Lørdag</th>
                    <th>Endre</th>
                    <th>Send Epost</th>
                    <th>Slett</th>
                    </thead>
                    <tbody>
                    <?php foreach ($gjester as $gjest) {
                        /* @var \intern3\HelgaGjesteObjekt $gjest */
                        ?>

                        <tr id="<?php echo $gjest->getIder()[0]; ?>">
                            <td>Navn: <?php echo $gjest->getNavn(); ?></td>
                            <td>Epost: <?php echo $gjest->getEpost(); ?></td>
                            <td data-label="<?php echo in_array("0",
                                $gjest->getDager()) ? '1' : '0'; ?>"><?php if (in_array("0", $gjest->getDager())) {
                                    echo "✗";
                                } ?></td>
                            <td data-label="<?php echo in_array("1",
                                $gjest->getDager()) ? '1' : '0'; ?>"><?php if (in_array("1", $gjest->getDager())) {
                                    echo "✗";
                                } ?></td>
                            <td data-label="<?php echo in_array("2",
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
        $(document).ready(function () {
            var table = $('#tabellen').DataTable({
                "paging": false,
                "searching": false,
                "info": false
            });

            $('#torsdag').click(function() {
                $.fn.dataTable.ext.search.push(
                    function (settings, data, dataIndex) {
                        return false;
                        return table.row(table.row(dataIndex).node().children[2]).data()[2].length > 0;
                    }
                );

                table.draw();
            })


        });

       /* $('#torsdag').on('click', function () {
            $.fn.dataTable.ext.search.push(
                function (settings, data, dataIndex) {
                    return table.row(table.row(dataIndex).node().children[2]).data()[2].length > 0;
                }
            );
            table.draw();
            $.fn.dataTable.ext.search.pop();
        })*/


    </script>

<?php

require_once(__DIR__ . '/../static/bunn.php');
