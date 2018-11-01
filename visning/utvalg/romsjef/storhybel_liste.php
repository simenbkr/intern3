<?php

require_once(__DIR__ . '/../topp_utvalg.php');

?>

    <div class="container">
        <div class="col-lg-12">
            <h1>Utvalget &raquo; Romsjef &raquo; StorhybellisteLISTE</h1>

            <hr>

            <table class="table table-responsive">

                <thead>
                <tr>
                    <th>Navn</th>
                    <th>Status (1=aktiv, 0=inaktiv)</th>
                    <th></th>
                </tr>

                </thead>

                <tbody>

                <?php foreach ($lista as $liste) {
                    /* @var \intern3\Storhybelliste $liste */

                    var_dump($liste->getVelgerNr());

                    if ($liste->getVelgerNr() > 0) {

                        ?>
                        <tr>
                            <td><?php echo $liste->getNavn(); ?></td>
                            <td><?php echo $liste->erAktiv() ? 'Aktiv' : 'Inaktiv'; ?></td>
                            <td><?php echo $liste->getVelger()->getFulltNavn(); ?>
                                (nr. <?php echo $liste->getVelgerNr(); ?></td>
                            <td><?php echo $liste->getNeste()->getFulltNavn(); ?></td>
                        </tr>

                    <?php } else { ?>
                        <tr>
                            <td><?php echo $liste->getNavn(); ?></td>
                            <td><?php echo $liste->erAktiv() ? 'Aktiv' : 'Inaktiv'; ?></td>
                            <td></td>
                            <td></td>

                        </tr>

                    <?php }
                      ?>
                <?php }
                  ?>
                </tbody>


            </table>


        </div>
    </div>


<?php

require_once(__DIR__ . '/../../static/bunn.php');
