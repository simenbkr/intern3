<?php
require_once('topp_utvalg.php');

/* @var $beboer \intern3\Beboer */

?>
    <div class="container">
        <h1>Utvalget » Vaktsjef » Vaktoversikt » Endre vaktantall for <?php echo $beboer->getFulltNavn(); ?></h1>
        <hr>
        <div class="tilbakemelding">
            <?php if (isset($_SESSION['success']) && isset($_SESSION['msg'])) { ?>

                <div class="alert alert-success fade in" id="success"
                     style="display:table; margin: auto; margin-top: 5%">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <?php echo $_SESSION['msg']; ?>
                </div>
                <p></p>
                <?php
            } elseif (isset($_SESSION['error']) && isset($_SESSION['msg'])) { ?>
                <div class="alert alert-danger fade in" id="danger" style="display:table; margin: auto; margin-top: 5%">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <?php echo $_SESSION['msg']; ?>
                </div>
                <p></p>
                <?php
            }
            unset($_SESSION['success']);
            unset($_SESSION['error']);
            unset($_SESSION['msg']);
            ?></div>

        <form action="" method="post">
            <table class="form table table-responsive table-bordered">
                <tr>
                    <th>Antall vakter:</th>
                    <td><input autofocus type="number" max="20" name="antall" placeholder="<?php echo $beboer->getBruker()->antallVakterSkalSitte(); ?>"</td>
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
<?php
require_once('bunn.php');
?>