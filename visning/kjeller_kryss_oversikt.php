<?php

require_once ('topp.php');

?>
    <script type="text/javascript" src="js/dataTables.js"></script>
    <link rel="stylesheet" type="text/css" href="css/dataTables.css"/>
    <script>
        $(document).ready(function () {
            var a = $('#tabellen').DataTable({
                "paging": true,
                "searching": true,
                "order": [[1, "desc"]]
            });


        });
    </script>
<div class="container">

    <h1>Kjellermester » Kryssoversikt</h1>

    <p>[ <a href="<?php echo $cd->getBase(); ?>kjeller/admin">Vinadministrasjon</a> ]
        [ <a href="<?php echo $cd->getBase(); ?>kjeller/slettet_vin">Slettet vin</a> ]
        [ <a href="<?php echo $cd->getBase(); ?>kjeller/leggtil">Legg til vin</a> ]
        [ <a href="<?php echo $cd->getBase(); ?>kjeller/add_type">Vintyper</a> ]
        [ <a href="<?php echo $cd->getBase(); ?>kjeller/pafyll">Påfyll</a> ]
        [ <a href="<?php echo $cd->getBase(); ?>kjeller/lister">Lister</a> ]
        [ <a href="<?php echo $cd->getBase(); ?>kjeller/regning">Regning</a> ]
        [ <a href="<?php echo $cd->getBase(); ?>kjeller/svinn">Svinn</a> ]
        [ <a href="<?php echo $cd->getBase(); ?>kjeller/lister/beboere_vin">Fakturer</a> ]
        [ Oversikt ]
        [ <a href="<?php echo $cd->getBase(); ?>kjeller/regler">Regler</a> ]
    </p>


    <div class="col-lg-12">
        <h2>Alle transaksjoner</h2>
        <table class="table table-bordered" id="tabellen">
            <thead>
            <th>Beboer</th>
            <th data-sortable="true">Tid</th>
            <th>Antall</th>
            <th>Drikke</th>
            <th>Pris</th>
            <th>Ansvarlig</th>
            </thead>
            <?php

            foreach ($transaksjoner as $kryss) {

                /* @var \intern3\Vinkryss $kryss */

                ?>
                <tr>
                    <td><?php echo $kryss->getBeboer()->getFulltNavn(); ?></td>
                    <td><?php echo $kryss->getTiden(); ?></td>
                    <td><?php echo round($kryss->getAntall(), 2); ?></td>
                    <td><?php echo $kryss->getVin()->getNavn(); ?></td>
                    <td><?php echo round($kryss->getPrisen(), 2); ?></td>
                    <td><?php echo $kryss->getAnsvarlig()->getFulltNavn(); ?></td>
                </tr>
                <?php
            }
           ?>
        </table>
    </div>
</div>
<?php

require_once ('bunn.php');

?>