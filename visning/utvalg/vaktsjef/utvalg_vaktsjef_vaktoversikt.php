<?php

require_once(__DIR__ . '/../topp_utvalg.php');

?>
<script>
    function endreVakter() {
        var host_halv = document.getElementById("1h").value;
        var vaar_halv = document.getElementById("1v").value;
        var host_hel = document.getElementById("2h").value;
        var vaar_hel = document.getElementById("2v").value;
        $.ajax({
            type: 'POST',
            url: '?a=utvalg/vaktsjef/vaktoversikt',
            data: 'endreVakt=1&hosthalv=' + host_halv + "&vaarhalv=" + vaar_halv + "&hosthel=" + host_hel + "&vaarhel=" + vaar_hel,
            method: 'POST',
            success: function (data) {
                location.reload();
            },
            error: function (req, stat, err) {
                alert(err);
            }
        });
    }
</script>
<div class="col-md-12">
    <h1>Utvalget &raquo; Vaktsjef &raquo; Vaktoversikt</h1>
    <hr>
</div>
<div class="row">
    <div class="col-md-6">
        <?php include(__DIR__ . '/../../static/tilbakemelding.php'); ?>

        <table class="table table-bordered">
            <tr>
                <th scope="row">Antall vakter:</th>
                <td><?php echo $antallVakter; ?></td>
            </tr>
            <tr>
                <th scope="row">Ufordelte vakter:</th>
                <td><?php echo $antallUfordelte; ?></td>
            </tr>
            <tr>
                <th scope="row">Ubekreftede vakter:</th>
                <td><?php echo $antallUbekreftet; ?></td>
            </tr>
        </table>

        <p>En kjip vakt er en vakt som oppfyller én eller flere av følgende krav:
        <ul>
            <li>Førstevakt</li>
            <li>Lørdagsvakt</li>
            <li>2. vakt søndag</li>
            <li>3., 4. vakt fredag</li>
        </ul>
        Dette utgjør 13 av 28 vakter i løpet av en uke.

        </p>

    </div>
    <div class="col-md-4" id="kake">
        <table class="table table-bordered table-responsive small">
            <tr>
                <th>Rolle</th>
                <th>Vakter Høst</th>
                <th>Vakter Vår</th>
            </tr>
            <?php foreach ($roller as $rollen) {
                if ($rollen->getNavn() == "Full regi") {
                    continue;
                } ?>
                <tr>
                    <td><?php echo $rollen->getNavn(); ?></td>
                    <td><input class="form-control" type="text" name="host" id="<?php echo $rollen->getId(); ?>h"
                               value="<?php echo $rollen->getVakterH(); ?>" size="1"></td>
                    <td><input class="form-control" type="text" name="vaar" id="<?php echo $rollen->getId(); ?>v"
                               value="<?php echo $rollen->getVakterV(); ?>" size="1"></td>
                </tr>
                <?php
            }
            ?>
            <tr>
                <td></td>
                <td>
                    <button class="btn btn-primary btn-sm" onclick="endreVakter()">Endre</button>
                </td>
                <td></td>
            </tr>
        </table>
    </div>
</div>

<div class="container">


<div class="col-md-12 table-responsive">
    <table class=" table-bordered " id="tabellen">
        <thead>
        <tr>
            <th>Navn</th>
            <th>Straffevakter</th>
            <th>Skal sitte</th>
            <th>Har sittet</th>
            <th>Er oppsatt</th>
            <th>Førstevakter</th>
            <th>Kjipe vakter</th>
            <th>Har igjen</th>
            <th>Ikke oppsatt</th>
            <th>Ikke bekreftet</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $sumStraffevakter = 0;
        $sumSkalSitte = 0;
        $sumHarSittet = 0;
        $sumErOppsatt = 0;
        $sumForstevakter = 0;
        $sumKjipevakter = 0;
        $sumVakterIgjen = 0;
        $sumIkkeOppsatt = 0;
        $sumIkkeBekreftet = 0;

        foreach ($beboerListe as $beboer) {
            /* @var $beboer \intern3\Beboer */
            $bruker = $beboer->getBruker();
            $advarsel = $bruker->vaktAdvarsel();

            ?>
            <tr>
                <td>
                    <a href="?a=utvalg/vaktsjef/vaktoversikt/endre/<?php echo $beboer->getId(); ?>"><?php echo $beboer->getFulltNavn(); ?></a>
                    <?php if ($bruker->harVakterTett()) { ?>
                        <span class="glyphicon glyphicon-time" title="Det ser ut som denne beboeren har vakter tett."></span>
                    <?php } if($bruker->harForMangeKjipeVakter()) { ?>
                    <span class="glyphicon glyphicon-trash" title="Det ser ut som denne beboeren har mange kjipe vakter."></span>
                    <?php } if($bruker->harForMangeForstevakter()) { ?>
                    <span class="glyphicon glyphicon-glyphicon-off" title="Det ser ut som denne beboeren har for mange førstevakter."></span>
                    <?php } ?>
                </td>
                <td><?php echo $bruker->antallStraffevakter(); $sumStraffevakter += $bruker->antallStraffevakter(); ?></td>
                <td><?php echo $bruker->antallVakterSkalSitte(); $sumSkalSitte += $bruker->antallVakterSkalSitte(); ?></td>
                <td><?php echo $bruker->antallVakterHarSittet(); $sumHarSittet += $bruker->antallVakterHarSittet(); ?></td>
                <td><?php echo $bruker->antallVakterErOppsatt(); $sumErOppsatt += $bruker->antallVakterErOppsatt(); ?></td>
                <td><?php echo $bruker->antallForstevakter(); $sumForstevakter += $bruker->antallForstevakter(); ?></td>
                <td><?php echo $bruker->getPerson()->antallKjipeVakter(); $sumKjipevakter += $bruker->getPerson()->antallKjipeVakter(); ?></td>
                <td><?php echo $bruker->antallVakterHarIgjen(); $sumVakterIgjen += $bruker->antallVakterHarIgjen(); ?></td>
                <td><?php echo $bruker->antallVakterIkkeOppsatt(); $sumIkkeOppsatt += $bruker->antallVakterIkkeOppsatt(); ?></td>
                <td><?php echo $bruker->antallVakterIkkeBekreftet(); $sumIkkeBekreftet += $bruker->antallVakterIkkeBekreftet(); ?></td>
            </tr>
            <?php
        }

        ?>
        </tbody>
        <tfoot>
            <tr>
                <th>SUM</th>
                <td><?php echo $sumStraffevakter?></td>
                <td><?php echo $sumSkalSitte?></td>
                <td><?php echo $sumHarSittet?></td>
                <td><?php echo $sumErOppsatt?></td>
                <td><?php echo $sumForstevakter?></td>
                <td><?php echo $sumKjipevakter?></td>
                <td><?php echo $sumVakterIgjen?></td>
                <td><?php echo $sumIkkeOppsatt?></td>
                <td><?php echo $sumIkkeBekreftet?></td>
            </tr>
        </tfoot>
    </table>

</div>
</div>

<link rel="stylesheet" type="text/css" href="css/dataTables.css"/>
<script type="text/javascript" src="js/dataTables.js"></script>

<script>

    $(document).ready(function () {
        $('#tabellen').DataTable({
            "paging": false,
            "searching": true,
            "scrollY": "500px",
            "scrollCollapse": true
        });
    });

</script>


<?php

require_once(__DIR__ . '/../../static/bunn.php');

?>
