<?php

require_once(__DIR__ . '/../static/topp.php');

?>

<script type="text/javascript" src="js/dataTables.js"></script>
<link rel="stylesheet" type="text/css" href="css/dataTables.css"/>
<div class="col-md-12">
    <h1>Kryss &raquo; Prisliste</h1>

    [ <a href="?a=kryss">Kryss historikk</a> ] | [ <a href="?a=kryss/statistikk">Kryssestatistikk</a> ] | [ Prisliste ]

    <hr>
</div>
<div class="col-md-4 col-sm-6">
    <h2>Resepsjonen</h2>
    <table class="table table-bordered" id="prisResep">
        <thead>
            <th data-sortable="true">Drikke</th>
            <th>Pris</th>
        </thead>
        <?php
            foreach ($drikker as $drikke) {
                if ($drikke->getAktiv() != 1) {
                    continue;
                }
                ?>
             <tr>
                 <td><?php echo $drikke->getNavn(); ?></td>
                 <td><?php echo $drikke->getPris(); ?>kr</td>
             </tr>
        <?php
        }

        ?>

    </table>
</div>
<div class="col-md-8 col-sm-6">
    <h2>Vinkjeller</h2>
    <table class="table table-striped table-bordered table-sm" cellspacing="0" id="prisKjeller">
        <thead>
            <th data-sortable="true">Drikke</th>
            <th>Type</th>
            <th>Pris</th>
        </thead>
        <?php
        foreach ($vin as $enhet) {
            if ($enhet->getSlettet() == 1) {
                continue;
            }
            ?>
            <tr>
                <td><?php echo $enhet->getNavn(); ?></td>
                <td><?php echo $enhet->getType()->getNavn(); ?></td>
                <td><?php echo round($enhet->getPris(), 2); ?>kr</td>
            </tr>
            <?php
        }

        ?>
    </table>
</div>

<script>
    $(document).ready(function () {
        $('#prisKjeller').DataTable({
            "scrollY": "400px",
            "scrollCollapse": true,
        });
        $('.dataTables_length').addClass('bs-select');
    });
</script>

