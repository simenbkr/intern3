<?php

require_once(__DIR__ . '/../topp_utvalg.php');

?>

    <div class="container">
        <div class="col-lg-12">
            <h1>Utvalget &raquo; Romsjef &raquo; StorhybellisteLISTE</h1>

            [ Liste ] | [ <a href="?a=utvalg/romsjef/storhybel"> Ny
                Storhybelliste</a> ] [ <a href="?a=utvalg/romsjef/storhybel/korr">Ny Korrhybelliste</a> ]
            [ <a href="?a=utvalg/romsjef/storhybel/storparhybel">Ny Parhybelliste</a> ]

            <hr>

            <?php require_once(__DIR__ . '/../../static/tilbakemelding.php'); ?>

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

                    $klassen = '';
                    if($liste->erAktiv()) {
                        $klassen = 'success';
                    }

                    if ($liste->getVelgerNr() > 0 && !$liste->erFerdig()) {

                        ?>
                        <tr class="<?php echo $klassen;?>" onclick="window.location='?a=utvalg/romsjef/storhybel/liste/<?php echo $liste->getId(); ?>'">
                            <td><?php echo $liste->getNavn(); ?></td>
                            <td><?php echo $liste->getStatusTekst(); ?></td>
                            <td><?php echo $liste->getVelger()->getNavn(); ?>
                                (nr. <?php echo $liste->getVelgerNr(); ?>)</td>
                            <td><?php echo $liste->getNeste()->getNavn(); ?></td>
                        </tr>

                    <?php } else { ?>
                        <tr class="<?php echo $klassen;?>" onclick="window.location='?a=utvalg/romsjef/storhybel/liste/<?php echo $liste->getId(); ?>'">
                            <td><?php echo $liste->getNavn(); ?></td>
                            <td><?php echo $liste->getStatusTekst(); ?></td>
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
