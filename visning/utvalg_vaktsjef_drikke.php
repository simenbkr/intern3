<?php
require_once ('topp_utvalg.php');
?>
<div class="container">
    <div class="col-lg-6">
    <h1>Utvalget » Vaktsjef » Drikke</h1>
    <hr>

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

        <div class="col-md-3">
            <form action="" method="POST">
                <table class="table table-bordered table-responsive small">
                    <input type="hidden" name="thingy" value="1">
                    <tr>
                        <th>Drikke</th>
                        <th>Pris</th>
                        <th>Aktiv</th>
                        <th>Farge</th>
                    </tr>
                    <?php foreach($drikke as $drikken){ ?>
                        <tr>
                            <td><a href="?a=utvalg/vaktsjef/endre_drikke/<?php echo $drikken->getId();?>"><?php echo $drikken->getNavn();?></a></td>
                            <td><?php echo $drikken->getPris();?></td>
                            <td><?php echo $drikken->getAktiv() ? 'Aktiv' : 'Inaktiv';?></td>
                            <td><?php echo $drikken->getFarge();?></td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
            </form>
        </div>

        <div class="col-md-12">
            <form action="" method="post" enctype="multipart/form-data">
                <table class="table table-bordered table-responsive">
                    <tr>
                        <td>Navn:</td>
                        <td><input type="text" name="navn" value=""></td>
                    </tr>
                    <tr>
                        <td>Pris:</td>
                        <td><input type="text" name="pris" value=""></td>
                    </tr>
                    <tr>
                        <td>Farge (på krysselista):</td>
                        <td><input type="color" name="farge" value="#000"></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><input class="btn btn-primary" type="submit" value="Legg til"></td>
                    </tr>
                </table>
            </form>
        </div>

    </div>
</div>
<?php
require_once ('bunn.php');
?>