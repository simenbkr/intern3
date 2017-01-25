<?php
require_once('topp.php');
?>
<div class="container">
    <h1>Kjellermester » Legg til vin</h1>
    <hr>
    <?php if (isset($error)){ ?>
    <div class="alert alert-danger fade in" id="success" style="display:table; margin: auto; margin-top: 5%">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        Noe gikk galt! Vinen ble ikke lagt til!
        <?php }
        unset($error) ?>
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
                    <td>Antall innkjøpt:</td>
                    <td><input type="text" name="antall"></td>
                </tr>
                <tr>
                    <td>Type:</td>
                    <td><select name="type">
                            <?php
                            foreach ($vintyper as $vintypen) {
                                ?>
                                <option
                                    value="<?php echo $vintypen->getId(); ?>"><?php echo $vintypen->getNavn(); ?></option>
                                <?php
                            }
                            ?>
                        </select></td>
                </tr>
                <tr>
                    <td>Bilde:</td>
                    <td><input type="file" name="image"/></td>
                </tr>
                <tr>
                    <td></td>
                    <td><input class="btn btn-primary" type="submit" value="Legg til"></td>
                </tr>
            </table>
        </form>
    </div>
</div>