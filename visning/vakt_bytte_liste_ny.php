<?php

require_once('topp.php');

/* @var \intern3\Beboer $beboer */
/* @var \intern3\VaktListe $egne_vakter */


?>

<div class="container">

    <div class="col-md-12">
        <h1>Vakt » Vaktbytte</h1>
        <p>[ <a href="?a=vakt">Vakt</a> ] [ Vaktbytte ]</p>

        <?php
        require_once('tilbakemelding.php');
        ?>
    </div>

    <div class="col-md-3 col-sm-6 col-sx-12">
        <table class="table table-bordered">

            <tr>
                <th>Dine vakter</th>
            </tr>

            <?php foreach ($egne_vakter as $vakt) {
                /* @var \intern3\Vakt $vakt */
                ?>
                <tr>
                    <?php

                    if ($vakt->erFerdig() || $vakt->erStraffevakt() || $vakt->getDato() == date('Y-m-d')) {
                        //Vakter brukere ikke kan gjøre noe med.
                        ?>

                        <td class="celle_graa"> <?php echo $vakt->toString(); ?></td>

                    <?php } elseif ($vakt->getVaktbytte() != null && $vakt->getVaktbytte()->getGisbort()) { ?>

                        <td class="celle_oransje"> <?php echo $vakt->toString(); ?></td>
                        <?php
                    } else {
                        if (!$vakt->getVaktbytte()) { ?>
                            <td><a href="?a=vakt/bytte/modal_egen/<?php echo $vakt->getId(); ?>" data-toggle="modal"
                                   data-target="#myModal" data-remote="false"
                                   class="btn btn-primary"><?php echo $vakt->toString(); ?></a></td>
                        <?php } else { ?>
                            <td>
                                <a href="?a=vakt/bytte/modal_forslag/<?php echo $vakt->getVaktbytte()->getId(); ?>"
                                   data-toggle="modal"
                                   data-target="#myModal" data-remote="false" class="btn btn-warning">
                                    <?php echo $vakt->toString(); ?>
                                    <span title="Byttes" class="glyphicon glyphicon-refresh"></span>
                                </a>

                            </td>
                            <?php
                        }
                    }
                    ?>
                </tr>

            <?php } ?>
        </table>
    </div>

    <div class="col-md-12">

        <?php
        foreach (range(1, 4) as $type) { ?>

            <div class="col-md-3 col-sm-6 col-sx-12">
                <table class="table table-bordered">
                    <tr>
                        <th><?php echo $type; ?>. vakt</th>
                    </tr>

                    <?php foreach ($vaktbytter as $vaktbytte) {
                        /* @var \intern3\Vaktbytte $vaktbytte */

                        if ($vaktbytte->getVakt()->getVakttype() != $type) {
                            continue;
                        }

                        ?>

                        <tr>
                            <td>

                                <?php

                                if ($vaktbytte->harPassord()) { ?>
                                    <span title="Passordlåst" class="glyphicon glyphicon-lock"></span>
                                <?php }
                                if ($vaktbytte->getGisBort()) { ?>
                                    <span title="Gis bort" class="glyphicon glyphicon-alert"></span>
                                    <?php
                                } else { ?>
                                    <span title="Byttes" class="glyphicon glyphicon-refresh"></span>
                                <?php }

                                if ($vaktbytte->getVakt()->getBrukerId() === $beboer->getBrukerId()) { ?>
                                    <a href="?a=vakt/bytte/modal_forslag/<?php echo $vaktbytte->getId(); ?>"
                                       data-toggle="modal"
                                       data-target="#myModal" data-remote="false" class="btn-sm btn-primary pull-right">
                                        Se forslag</a>
                                    <a href="?a=vakt/bytte/modal_slett/<?php echo $vaktbytte->getId(); ?>"
                                       data-toggle="modal"
                                       data-target="#myModal" data-remote="false" class="btn-sm btn-danger pull-right">
                                        Trekk</a>
                                <?php } else {
                                    if (!$vaktbytte->getGisBort()) { ?>
                                        <a href="?a=vakt/bytte/modal_bytt/<?php echo $vaktbytte->getId(); ?>"
                                           data-toggle="modal"
                                           data-target="#myModal" data-remote="false"
                                           class="btn-sm btn-info pull-right">Bytt</a>
                                        <?php
                                    } else { ?>
                                        <a href="?a=vakt/bytte/modal_gibort/<?php echo $vaktbytte->getId(); ?>"
                                           data-toggle="modal"
                                           data-target="#myModal" data-remote="false"
                                           class="btn-sm btn-warning pull-right">
                                            Ta vakt</a>
                                        <?php
                                    }

                                }

                                echo $vaktbytte->getVakt()->shortToString();
                                echo "<br/>";
                                echo $vaktbytte->getVakt()->getBruker()->getPerson()->getFulltNavn();

                                if($vaktbytte->getMerknad() != null && strlen($vaktbytte->getMerknad()) > 1){
                                    echo "<br/>";
                                    echo $vaktbytte->getMerknad();
                                }

                                ?>

                            </td>
                        </tr>
                    <?php }
                      ?>
                </table>
            </div>
        <?php }
        ?>
    </div>
</div>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Vaktbytte</h4>
            </div>
            <div class="modal-body">
                ...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Lukk</button>
            </div>
        </div>
    </div>
</div>
<script>
    // Fill modal with content from link href
    $("#myModal").on("show.bs.modal", function (e) {
        var link = $(e.relatedTarget);
        $(this).find(".modal-body").load(link.attr("href"));
    });
</script>
<?php

require_once('bunn.php');

?>
