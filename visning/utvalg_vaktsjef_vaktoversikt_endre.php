<?php
require_once('topp_utvalg.php');

/* @var $beboer \intern3\Beboer */

?>
    <div class="container">
        <h1>Utvalget » Vaktsjef » Vaktoversikt » Endre vaktantall for <?php echo $beboer->getFulltNavn(); ?></h1>
        <hr>

        <?php require_once ('tilbakemelding.php'); ?>

        <div class="col-md-6">
            <h2>Endre antall vakter:</h2>
            <form action="" method="post">
                <table class="form table table-responsive table-bordered">
                    <tr>
                        <th>Antall vakter:</th>
                        <td><input autofocus type="number" max="20" name="antall"
                                   placeholder="<?php echo $beboer->getBruker()->antallVakterSkalSitte(); ?>"</td>
                    </tr>
                    <tr>
                        <th>Semester:</th>
                        <td>
                            <select name="semester">
                                <?php
                                foreach ($options as $option) {
                                    $tmp = str_replace("a", "å", $option);
                                    $tmp = str_replace("o", "ø", $tmp);
                                    $tmp = ucfirst(str_replace("-", " ", $tmp));
                                    ?>
                                    <option value="<?php echo $option; ?>"><?php echo $tmp; ?></option>
                                <?php }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <td></td>
                    <td><input type="submit" class="btn btn-primary" value="Endre"></td>
                    </tr>
                </table>
            </form>
        </div>

        <div class="col-md-6">
            <h2>Vakter oppsatt:</h2>
            <table class="table table-bordered table-responsive">
                <tr>
                    <td><b>Type</b></td>
                    <td><b>Dato</b></td>
                </tr>
                <?php
                foreach ($vakter as $vakt) {
                    /* @var $vakt \intern3\Vakt */
                    if ($vakt->erFerdig()) {
                        continue;
                    }
                    ?>
                    <tr>
                        <td><?php echo $vakt->getVaktType(); ?>. vakt</td>
                        <td><?php echo $vakt->getDato(); ?></td>

                    </tr>
                    <?php
                }
                ?>
            </table>

            <h2>Vakter sittet:</h2>
            <table class="table table-bordered table-responsive">
                <tr>
                    <td><b>Type</b></td>
                    <td><b>Dato</b></td>
                </tr>
                <?php
                foreach ($vakter as $vakt) {
                    /* @var $vakt \intern3\Vakt */
                    if (!$vakt->erFerdig()) {
                        continue;
                    }
                    ?>
                    <tr>
                        <td><?php echo $vakt->getVaktType(); ?>. vakt</td>
                        <td><?php echo $vakt->getDato(); ?></td>

                    </tr>
                    <?php
                }
                ?>
            </table>

        </div>

    </div>
<?php
require_once('bunn.php');
?>