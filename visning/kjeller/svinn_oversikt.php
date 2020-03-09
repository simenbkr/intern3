<?php
require_once(__DIR__ . '/../static/topp.php');
?>
    <link rel="stylesheet" type="text/css" href="css/dataTables.css"/>
    <script type="text/javascript" src="js/dataTables.js"></script>
    <script>
        var table;
        $(document).ready(function () {
            table = $('#tabellen').DataTable({
                "paging": false,
                "searching": false,
                "order": [[5, "desc"]]
            });
        });
    </script>
    <div class="container">
        <h1>Kjellermester » Registrert svinn</h1>
        <p>[ <a href="<?php echo $cd->getBase(); ?>kjeller/admin">Vinadministrasjon</a> ]
            [ <a href="<?php echo $cd->getBase(); ?>kjeller/slettet_vin">Slettet vin</a> ]
            [ <a href="<?php echo $cd->getBase(); ?>kjeller/leggtil">Legg til vin</a> ]
            [ <a href="<?php echo $cd->getBase(); ?>kjeller/add_type">Vintyper</a> ]
            [ <a href="<?php echo $cd->getBase(); ?>kjeller/pafyll">Påfyll</a> ]
            [ <a href="<?php echo $cd->getBase(); ?>kjeller/lister">Lister</a> ]
            [ <a href="<?php echo $cd->getBase(); ?>kjeller/regning">Regning</a> ]
            [ <a href="<?php echo $cd->getBase(); ?>kjeller/svinn">Svinn</a> ]
            [ Registrert svinn ]
            [ <a href="<?php echo $cd->getBase(); ?>kjeller/lister/beboere_vin">Fakturer</a> ]
            [ <a href="<?php echo $cd->getBase(); ?>kjeller/oversikt">Oversikt</a> ]
            [ <a href="<?php echo $cd->getBase(); ?>kjeller/regler">Regler</a> ]
        </p>
        <hr>
        <?php include(__DIR__ . '/../static/tilbakemelding.php'); ?>
        <div class="col-md-12">

            <table id="tabellen" class="table table-bordered table-responsive" data-toggle="table">
                <thead>
                <tr>
                    <th data-sortable="true">Navn</th>
                    <th data-sortable="true">Pris (innkjøp)</th>
                    <th data-sortable="true">Antall svinn</th>
                    <th data-sortable="true">Type</th>
                    <th data-sortable="true">Land</th>
                    <th>Registrert</th>
                    <th>Tidspunkt</th>
                </tr>
                </thead>
                <tbody>

                <?php foreach ($svinn as $s) {
                    /* @var \intern3\Vinsvinn $s */

                    if(is_null($s->getVin())) { ?>
                        <tr>
                            <td>Ukjent</td>
                            <td>Ukjent</td>
                            <td><?php echo $s->getAntall(); ?></td>
                            <td>Ukjent</td>
                            <td>Ukjent</td>
                            <td><?php echo $s->getRegistrert(); ?></td>
                            <td><?php echo $s->getTidspunkt(); ?></td>
                        </tr>
                        <?php
                    } else {
                        ?>
                        <tr>
                            <td><?php echo $s->getVin()->getNavn(); ?></td>
                            <td><?php echo $s->getVin()->getPris(); ?></td>
                            <td><?php echo $s->getAntall(); ?></td>
                            <td><?php echo $s->getVin()->getLand(); ?></td>
                            <td><?php echo $s->getVin()->getType()->getNavn(); ?></td>
                            <td><?php echo $s->getRegistrert(); ?></td>
                            <td><?php echo $s->getTidspunkt(); ?></td>
                        </tr>
                        <?php
                    }
                } ?>
                </tbody>
            </table>

        </div>
    </div>
<?php
require_once(__DIR__ . '/../static/bunn.php');
?>