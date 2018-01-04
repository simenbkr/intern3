<?php
/* @var \intern3\Verv $vervet */

require_once ('topp_utvalg.php');

?>

<div class="container">
    <div class="col-lg-12">
        <h1>Utvalg » Åpmandsverv » Endre <?php echo $vervet->getNavn(); ?></h1>
        <hr>
        <?php require_once ('tilbakemelding.php'); ?>
        <form action="" method="POST" onsubmit="">
            <table class="table table-bordered table-responsive">
                <tr>
                    <td>Navn:</td>
                    <td><input class="form-control" type="text" name="navn" value="<?php echo $vervet->getNavn(); ?>"></td>
                </tr>
                <tr>
                    <td>Beskrivelse:</td>
                    <td><textarea class="form-control" rows="10" cols="50" name="beskrivelse"><?php echo $vervet->getBeskrivelse(); ?></textarea></td>
                </tr>
                <tr>
                    <td>Åpmend</td>
                    <td><?php
                        $str = "";
                        foreach ($vervet->getApmend() as $beboer) {
                            if ($beboer != null) {
                                $str .= '<a href="?a=beboer/' . $beboer->getId() . '">' . $beboer->getFulltNavn() .
                                    '</a>, <button onclick="fjern(' . $beboer->getId() . ',' . $vervet->getId() . ')">&#x2718;</button>';
                            }
                        }
                        echo rtrim($str, ', '); ?></td>
                </tr>
                <tr>
                    <td></td>
                    <td><input class="btn btn-primary" type="submit" value="Endre" name="endre"></td>
                </tr>
            </table>
        </form>
    </div>
</div>

<script>
    function fjern(beboerId,vervId) {
        $.ajax({
            type: 'POST',
            url: '?a=utvalg/sekretar/apmandsverv',
            data: 'fjern=' + beboerId +'&verv='+ vervId,
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

<?php

require_once ('bunn.php');

?>