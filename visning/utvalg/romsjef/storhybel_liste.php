<?php

require_once(__DIR__ . '/../topp_utvalg.php');

?>

    <div class="container">
        <div class="col-lg-12">
            <h1>Utvalget &raquo; Romsjef &raquo; StorhybellisteLISTE</h1>

            [ Liste ] | [ <a href="?a=utvalg/romsjef/storhybel">Ny</a> ]

            <hr>

            <p>(Listen er klikkbar)</p>
            <table class="table table-responsive table-hover ui-selectable">

                <thead>
                <tr>
                    <th>Navn</th>
                    <th>Status</th>
                    <th>Nåværende velger</th>
                    <th>Neste</th>
                </tr>

                </thead>

                <tbody>

                <?php foreach ($lista as $liste) {
                    /* @var \intern3\Storhybelliste $liste */

                    if ($liste->getVelgerNr() > 0) {

                        ?>
                        <tr onclick="window.location='?a=utvalg/romsjef/storhybel/liste/<?php echo $liste->getId(); ?>'">
                            <td><?php echo $liste->getNavn(); ?></td>
                            <td><?php echo $liste->erAktiv() ? 'Aktiv' : 'Inaktiv'; ?></td>
                            <td><?php echo $liste->getVelger()->getFulltNavn(); ?>
                                (nr. <?php echo $liste->getVelgerNr(); ?>)</td>
                            <td><?php echo $liste->getNeste()->getFulltNavn(); ?></td>
                        </tr>

                    <?php } else { ?>
                        <tr onclick="window.location='?a=utvalg/romsjef/storhybel/liste/<?php echo $liste->getId(); ?>'">
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
