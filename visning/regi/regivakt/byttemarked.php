<?php

require_once(__DIR__ . '/../../static/topp.php');

$df = new \IntlDateFormatter('nb_NO',
    \IntlDateFormatter::MEDIUM, \IntlDateFormatter::NONE,
    'Europe/Oslo');

?>
    <div class="container">
        <div class="col-lg-12">
            <h1>Regi » Regivakt » Bytte</h1>
            <hr>
            <p>[ <a href="?a=regi/regivakt">Oversikt</a> ] | [ Bytte ]</p>

            <?php require_once(__DIR__ . '/../../static/tilbakemelding.php'); ?>

            <hr>

            <?php if (count($mine_vakter) > 0) { ?>

                <div class="col-md-4">
                    <h3>Dine regivakter:</h3>
                    <table class="table table-responsive table-bordered table-condensed">
                        <?php foreach ($mine_vakter as $rv) {
                            /* @var \intern3\Regivakt $rv */

                            $knapp = "onclick=\"vis('{$rv->getId()}')\"";
                            $knapp2 = 'disabled=disabled';
                            if (!is_null(\intern3\Regivaktbytte::medRegivaktIdBrukerId($rv->getId(),
                                $aktiv_bruker->getId()))) {
                                $knapp = 'disabled=disabled';
                                $knapp2 = "onclick=\"fjern('{$rv->getId()}')\"";
                            }

                            if (in_array($rv->getStatusInt(), [1, 2, 3])) {
                                $knapp = 'disabled=disabled';
                            }
                            ?>
                            <tr>
                                <td><?php echo $rv->getNokkelord(); ?></td>
                                <td>Tidspunkt:</td>
                                <td>
                                    <button class="btn btn-info btn-sm" <?php echo $knapp; ?>>
                                        <?php echo "{$df->format(strtotime($rv->getDato()))} {$rv->getStartTid()}-{$rv->getSluttTid()}"; ?>
                                    </button>
                                </td>
                                <td>
                                    <button class="btn btn-danger btn-sm" <?php echo $knapp2; ?>>Fjern fra alle bytter
                                    </button>
                                </td>
                            </tr>
                        <?php } ?>
                    </table>
                    <hr>
                </div>

            <?php } ?>

            <div class="col-lg-12">

                <table class="table table-responsive table-bordered">
                    <thead>
                    <tr>
                        <th>Bytter</th>
                        <th>Tidspunkt</th>
                        <th>Byttes/Gisbort</th>
                        <th>Passord</th>
                        <th>Merknad</th>
                        <th>Beskrivelse</th>
                        <th></th>
                    </tr>
                    </thead>

                    <tbody>
                    <?php foreach ($bytter as $bytte) {
                        /* @var \intern3\Regivaktbytte $bytte */

                        $disabled = '';
                        if (in_array($aktiv_bruker->getId(), $bytte->getRegivakt()->getBrukerIder())) {
                            $disabled = 'disabled="disabled"';
                        }

                        ?>
                        <tr>
                            <td><?php echo $bytte->getBruker()->getPerson()->getFulltNavn(); ?></td>
                            <td><?php echo ucfirst($bytte->getRegivakt()->medToString()) . ": {$bytte->getRegivakt()->getStartTid()} - {$bytte->getRegivakt()->getSluttTid()}"; ?></td>
                            <td><?php echo $bytte->gisbort() ? 'Gis bort' : 'Byttes'; ?></td>
                            <td><?php echo $bytte->harPassord() ? '✗' : ''; ?></td>
                            <td><?php echo $bytte->getMerknad(); ?></td>
                            <td><?php echo $bytte->getRegivakt()->getBeskrivelse(); ?></td>

                            <?php if ($bytte->gisbort() && !$bytte->harPassord()) { ?>
                                <td>
                                    <button class="btn btn-primary" <?php echo $disabled; ?>
                                            onclick="gisbort('<?php echo $bytte->getId(); ?>')">
                                        <span title="Gis bort" class="glyphicon glyphicon-alert"></span>
                                        Ta regivakt!
                                    </button>
                                </td>
                            <?php } elseif ($bytte->gisbort() && $bytte->harPassord()) { ?>
                                <td>
                                    <button class="btn btn-primary" <?php echo $disabled; ?>
                                            onclick="gisbort('<?php echo $bytte->getId(); ?>')">
                                        <span title="Passordlåst" class="glyphicon glyphicon-lock"></span>
                                        Ta regivakt!
                                    </button>
                                </td>
                            <?php } elseif ($aktiv_bruker->getId() == $bytte->getBrukerId()) { ?>
                                <td>
                                    <button class="btn btn-primary"
                                            onclick="bytte('<?php echo $bytte->getId(); ?>')">
                                        <span title="Byttes" class="glyphicon glyphicon-refresh"></span>
                                        Se forslag
                                    </button>
                                </td>
                            <?php } elseif (!$bytte->gisbort() && !$bytte->harPassord()) { ?>
                                <td>
                                    <button class="btn btn-primary" <?php echo $disabled; ?>
                                            onclick="forslag('<?php echo $bytte->getId(); ?>')">
                                        <span title="Byttes" class="glyphicon glyphicon-refresh"></span>
                                        Legg inn forslag
                                    </button>
                                </td>
                            <?php } else { ?>
                                <td>
                                    <button class="btn btn-primary" <?php echo $disabled; ?>
                                            onclick="forslag('<?php echo $bytte->getId(); ?>')">
                                        <span title="Passordlåst" class="glyphicon glyphicon-lock"></span>
                                        Legg inn forslag
                                    </button>
                                </td>
                            <?php }
                              ?>
                        </tr>
                    <?php }
                      ?>
                    </tbody>


                </table>


            </div>

        </div>
    </div>

    <!-- Modal! -->

    <div class="modal fade" id="modal-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="modal-tittel"></h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="mod">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Lukk</button>
                </div>
            </div>
        </div>
    </div>

    <script>

        function fjern(id) {
            $.ajax({
                cache: false,
                type: 'POST',
                url: '?a=regi/regivakt/fjern/' + id,
                success: function (data) {
                    location.reload();
                }
            });
        }

        function vis(id) {
            $("#mod").load("?a=regi/regivakt/vis_bytte/" + id);
            $("#modal-modal").modal("show");
        }

        function gisbort(id) {
            $("#mod").load("?a=regi/regivakt/gisbort/" + id);
            $("#modal-modal").modal("show");
        }

        function forslag(id) {
            $("#mod").load("?a=regi/regivakt/forslag/" + id);
            $("#modal-modal").modal("show");
        }

        function bytte(id) {
            $("#mod").load("?a=regi/regivakt/se_forslag/" + id);
            $("#modal-modal").modal("show");
        }

    </script>
<?php
require_once(__DIR__ . '/../../static/bunn.php');