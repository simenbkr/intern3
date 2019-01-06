<?php

require_once(__DIR__ . '/../static/topp.php');

/* @var \intern3\Arbeid $arbeidet */

?>

    <div class="col-md-12">
        <h1>Regi &raquo; Min regi &raquo; Detaljert</h1>

        <hr>

        <table class="table table-responsive table-striped">
            <tr>
                <th>Utført av:</th>
                <td><?php echo $arbeidet->getBruker()->getPerson()->getFulltNavn(); ?></td>
            </tr>
            <tr>
                <th>Dato utført:</th>
                <td><?php echo $arbeidet->getTidUtfort(); ?></td>
            </tr>
            <tr>
                <th>Dato registrert:</th>
                <td><?php echo $arbeidet->getTidRegistrert(); ?></td>
            </tr>
            <tr>
                <th>Kategori:</th>
                <td><?php echo $arbeidet->getPolymorfKategori()->getNavn(); ?></td>
            </tr>
            <tr>
                <th>Tid brukt:</th>
                <td><?php echo $arbeidet->getTidBrukt(); ?></td>
            </tr>
            <tr>
                <th>Kommentar:</th>
                <td><?php echo $arbeidet->getKommentar(); ?></td>
            </tr>
            <tr>
                <th>Status:</th>
                <td><?php echo $arbeidet->getStatus(); ?></td>
            </tr>

            <tr>
                <th>Tilbakemelding:</th>
                <td><?php echo $arbeidet->getTilbakemelding(); ?></td>
            </tr>

        </table>


        <?php if ($arbeidet->getIntStatus() == 0) { ?>

            <h3>Last opp (flere) bilder:</h3>
            <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" enctype='multipart/form-data'>
                <table class="table table-bordered">
                    <tr>
                        <th>Bilder</th>
                        <td>
                            <input type="file" name="file[]" multiple id="file">
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><input type="submit" class="btn btn-primary" name="registrer" value="Registrer"></td>
                    </tr>
                </table>
            </form>

        <?php }
        ?>

        <?php if (count($arbeidet->getArbeidBilder()) > 0) { ?>
            <p>
                <a class="btn btn-primary" data-toggle="collapse" href="#bilder" role="button" aria-expanded="false"
                   aria-controls="collapseExample">
                    Vis bilder
                </a>
            </p>
            <div class="collapse" id="bilder" data-toggle="collapse" href="#">
                <table class="table table-responsive">
                    <?php

                    foreach ($arbeidet->getArbeidBilder() as $arbeidbilde) {
                        /* @var \intern3\ArbeidBilde $arbeidbilde */
                        ?>
                        <tr>
                            <th>Bilde:</th>
                            <td>
                                <a href="<?php echo $arbeidbilde->getPath(); ?>">
                                    <img class="img-responsive" alt="bilde elns"
                                         src="<?php echo $arbeidbilde->getPath(); ?>">
                                </a>
                            </td>
                        </tr>
                        <?php
                    }


                    ?>
                </table>
            </div>
        <?php } ?>

    </div>


<?php

require_once(__DIR__ . '/../static/bunn.php');
