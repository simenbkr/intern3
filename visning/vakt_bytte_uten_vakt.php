<?php
require_once('topp.php');
$visBytteListe = true;
?>
    <div class="col-md-12">
        <h1>Vakt &raquo; Vaktbytte</h1>
        <p>[ <a href="<?php echo $cd->getBase(); ?>vakt">Vaktliste</a> ] [ Vaktbytte ]</p>
        <p>Du har ikke vakt, og kan (såklart) ikke bytte vakt!</p>
    </div>
    <div class="container">
<?php
if (isset($visBytteListe)) { ?>
    <div class="col-md-12"></div>
    <?php
    foreach (range(1, 4) as $vakttype) {
        ?>
        <div class="col-md-3 col-sm-6 col-sx-12">
            <table class="table table-bordered">
                <tr>
                    <th><?php echo $vakttype; ?>.&nbsp;vakt</th>
                </tr>
                <?php
                foreach ($vaktbytteListe[$vakttype] as $vaktbytte) {
                    $bruker = $vaktbytte->getVakt()->getBruker();
                    if ($bruker == null) {
                        //continue;
                    }
                    $modalId = 'modal-' . date('m-d', strtotime($vaktbytte->getVakt()->getDato())) . '-' . $vaktbytte->getVakt()->getVakttype();
                    ?>
                    <tr>
                    <td>
                        <fieldset disabled>
                        <?php

                        if ($vaktbytte->harPassord()) {
                            echo "<span title=\"Passordlåst\" class=\"glyphicon glyphicon-lock\"></span>";
                        }
                        if ($vaktbytte->getGisBort()) {
                            echo "<span title=\"Gis bort\" class=\"glyphicon glyphicon-alert\"></span>";
                        } else {
                            echo "<span title=\"Byttes\" class=\"glyphicon glyphicon-refresh\"></span>";
                        }
                        if ($vaktbytte->getVakt()->getBrukerId() != $cd->getAktivBruker()->getId() && $vaktbytte->getGisBort()) {
                            echo '<input type="button" class="btn btn-sm btn-info pull-right" value="Ta vakt" data-toggle="modal"';
                        } elseif ($vaktbytte->getVakt()->getBrukerId() != $cd->getAktivBruker()->getId() && !$vaktbytte->getGisBort()) {
                            echo '<input type="button" class="btn btn-sm btn-info pull-right" value="Bytt" data-toggle="modal"';
                        } else { ?>
                            <input class="btn btn-sm btn-danger pull-right" type="button" value="Trekk">
                            <?php
                            if (!$vaktbytte->getGisBort() && $har_vakt) { ?>
                                <input class="btn btn-sm btn-warning pull-right" type="button"
                                       value="Se forslag">
                                <?php
                            }
                        }
                        echo '<strong>' . ucfirst(strftime('%A %d/%m', strtotime($vaktbytte->getVakt()->getDato()))) . '</strong>' . PHP_EOL;
                        echo '<br>' . PHP_EOL;
                        echo ($bruker != null && $bruker->getPerson() != null) ? $bruker->getPerson()->getFulltNavn() : 'Fritz Müller';
                        $merknaden = $vaktbytte->getMerknad();
                        if ($merknaden != null) {
                            echo "<br/>" . $vaktbytte->getMerknad();
                        }

                        ?></fieldset>
                    </td>
                    </tr><?php } ?>
            </table>
        </div>
        <?php
    }
}
?>
<?php
require_once('bunn.php');
?>