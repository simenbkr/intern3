<?php
require_once('topp.php');
?>
<div class="container">
    <div class="col-md-12">
        <h1>Beboer &raquo; Gamle Beboere</h1>
        <p>[ <a href="<?php echo $cd->getBase(); ?>beboer">Beboerliste</a> ] [ <a
                href="<?php echo $cd->getBase(); ?>beboer/utskrift">Utskriftsvennlig</a> ] [ <a
                href="<?php echo $cd->getBase(); ?>beboer/statistikk">Statistikk</a> ]
            [ <a href="<?php echo $cd->getBase(); ?>beboer/kart">Beboerkart</a> ]
            [ Gamle Beboere ]
        </p>


        <table class="table table-bordered table-responsive">
            <thead>
                <td>Navn</td>
                <td>Adresse</td>
            </thead>
            <tbody>
            <?php

            foreach($beboerlista as $beboer){
                /* Gidder ikke ta med de uten adresser. */
                if($beboer == null || strlen($beboer->getAdresse()) < 3){
                    continue;
                }
                ?>
                <tr>
                    <td><?php echo $beboer->getFulltNavn();?></td>
                    <td><?php echo $beboer->getAdresse(); ?></td>
                </tr>

            <?php
            }
            ?>
            </tbody>

        </table>

    </div>
</div>


<?php
require_once('bunn.php');
?>
