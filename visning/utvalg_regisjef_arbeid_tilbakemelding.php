<?php
require_once('topp_utvalg.php');
?>
    <div class="container">

        <h1>Utvalget » Regisjef » Arbeid » Tilbakemelding</h1>

        <div class="tilbakemelding">
            <?php if (isset($_SESSION['success']) && isset($_SESSION['msg'])) { ?>

                <div class="alert alert-success fade in" id="success" style="display:table; margin: auto; margin-top: 5%">
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

        <table class="form table table-responsive table-bordered">
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
                <td><?php
                    if ($arbeidet->getGodkjent()) {
                        if ($arbeidet->getGodkjentBruker() != null && ($personen = $arbeidet->getGodkjentBruker()->getPerson()) != null) {
                            echo 'Godkjent: ' . $arbeidet->getTidGodkjent() . ' av ' . $personen->getFulltNavn();
                        } else {
                            echo 'Godkjent';
                        }
                    }
                    elseif($arbeidet->getStatus() == 'Underkjent'){
                        if ($arbeidet->getGodkjentBruker() != null && ($personen = $arbeidet->getGodkjentBruker()->getPerson()) != null) {
                            echo 'Underkjent: ' . $arbeidet->getTidGodkjent() . ' av ' . $personen->getFulltNavn();
                        } else {
                            echo 'Underkjent';
                        }
                    }
                    else {
                        echo $arbeidet->getStatus();
                    }
                    ?></td>
            </tr>
        </table>
        <form action="" method="post">
            <table class="form table table-responsive table-bordered">
                <tr>
                    <td><textarea class="form-control" rows="10" name="tilbakemelding"></textarea></td>
                </tr>
                <tr>
                    <td><input type="submit" class="btn btn-primary" value="Send tilbakemelding"></td>
                </tr>


            </table>
        </form>


    </div>
<?php
require_once('bunn.php');
?>