<?php
require_once(__DIR__ . '/../topp_utvalg.php');

?>
<div class="container">
<h1>Utvalget » Vaktsjef » Endre Drikke</h1>
    <hr>

    <form action="" method="post" enctype="multipart/form-data">
        <table class="table table-bordered table-responsive">
            <tr>
                <td>Navn:</td>
                <?php /*<td><input type="text" name="navn" value="<?php echo $drikka->getNavn(); ?>"></td>*/?>
                <td><?php echo $drikka->getNavn();?></td>
            </tr>
            <tr>
                <td>Pris:</td>
                <td><input type="text" name="pris" value="<?php echo $drikka->getPris(); ?>"></td>
            </tr>
            <tr>
                <td>Farge:</td>
                <td><input type="color" name="farge" value="<?php echo $drikka->getFarge();?>"</td>
            </tr>
            <tr>
                <td>Aktiv</td>
                <td><input type="checkbox" name="aktiv" <?php echo $drikka->getAktiv() ? 'checked=checked' :'';?></td>
            </tr>
            <tr>
                <td></td>
            <td><input type="submit" class="btn btn-primary"></td>
            </tr>
        </table>
    </form>
</div>
<?php
require_once(__DIR__ . '/../../static/bunn.php');
?>