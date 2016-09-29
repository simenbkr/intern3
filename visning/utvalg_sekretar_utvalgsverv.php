<?php

require_once('topp_utvalg.php');

?>
<div class="col-md-12">
    <h1>Utvalget &raquo; Sekretær &raquo; Utvalgsverv</h1>


    <p></p>
    <h2>Endre utvalgsverv:</h2>

</div>

<div class="col-md-6">
    <form action="" method="post">
    <table class="table-bordered table">
        <tr>
            <th>Utvalgsverv</th>
            <th>Beboer</th>
        </tr>
        <tr>
            <td><select name="vervid">

                    <?php
                    foreach ($vervListe as $verv) {
                        ?>

                        <option name="vervid" value="<?php echo $verv->getId(); ?>">
                            <?php echo $verv->getNavn(); ?>
                        </option>

                        <?php
                    }
                    ?>
            </td>
            <td><select name="beboerid">

                    <?php
                    foreach ($beboerListe as $beboer) {
                        ?>

                        <option name="beboerid" value="<?php echo $beboer->getId(); ?>">
                            <?php echo $beboer->getFulltNavn(); ?>
                        </option>

                        <?php
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
        <td></td>
        <td><input type="submit" class="btn btn-sm btn-info" value="Endre" name="endre"></td>
        </tr>
    </table>

    </form>



</div>

<?php

require_once('bunn.php');

?>
