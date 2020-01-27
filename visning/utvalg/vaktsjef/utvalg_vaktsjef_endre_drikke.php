<?php
require_once(__DIR__ . '/../topp_utvalg.php');

?>

    <script>
        function setForst() {
            $.ajax({
                type: 'POST',
                url: '?a=utvalg/vaktsjef/drekkefolge/<?php echo $drikka->getId(); ?>',
                method: 'POST',
                success: function (data) {
                    window.location.reload();
                },
                error: function (req, stat, err) {
                    alert(err);
                }
            });
        }
    </script>

    <div class="container">
        <h1>Utvalget » Vaktsjef » Endre Drikke: <?php echo $drikka->getNavn(); ?></h1>
        <?php if ($drikka->erForst()) { ?>
            <button class="btn btn-primary" onclick="setForst()">Fjern fra førstevalg</button>
        <?php } else { ?>
            <button class="btn btn-primary" onclick="setForst()">Sett til førstevalg</button>
        <?php } ?>

        <?php require_once __DIR__ . '/../../static/tilbakemelding.php'; ?>
        <hr>

        <form action="" method="post" enctype="multipart/form-data">
            <table class="table table-bordered table-responsive">
                <tr>
                    <td>Navn:</td>
                    <?php /*<td><input type="text" name="navn" value="<?php echo $drikka->getNavn(); ?>"></td>*/ ?>
                    <td><?php echo $drikka->getNavn(); ?></td>
                </tr>
                <tr>
                    <td>Pris:</td>
                    <td><input class="form-control" type="text" name="pris" value="<?php echo $drikka->getPris(); ?>">
                    </td>
                </tr>
                <tr>
                    <td>Farge:</td>
                    <td><input type="color" name="farge" value="<?php echo $drikka->getFarge(); ?>"></td>
                </tr>
                <tr>
                    <td>Aktiv:</td>
                    <td><input type="checkbox" name="aktiv" <?php echo $drikka->getAktiv() ? 'checked=checked' : ''; ?>>
                    </td>
                </tr>
                <tr>
                    <td>Kommentar:</td>
                    <td><textarea class="form-control" placeholder="'Liste med tilhørende drikke...'"
                                  name="kommentar"><?php echo $drikka->getKommentar(); ?></textarea></td>
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