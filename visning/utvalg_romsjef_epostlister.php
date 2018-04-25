<?php

require_once('topp_utvalg.php');

/* @var \intern3\BeboerListe $beboerliste */


?>

<script>
    function sjekk(id) {
        $("#" + id).load("?a=utvalg/romsjef/epost/" + id);
    }

    function leggTil(id, group) {
        $("#" + id + " ." + group.split("@")[0]).load("?a=utvalg/romsjef/epost/" + group + "/" + id, {"group": group, "id": id});
    }


</script>
<div class="container">
    <h1>Utvalget &raquo; Romsjef &raquo; Epostlister</h1>
    <hr>
    <div class="col-md-12">


        <table class="table table-bordered table-responsive">
            <thead>
            <tr>
                <td>Navn</td>
                <td>Epost</td>
                <td>SING-ALLE</td>
                <td>SING-SLARV</td>
                <td>SING-JENTER</td>
                <td>SING-GUTTER</td>
                <td>Sjekk</td>
            </tr>


            </thead>
            <tbody>
            <?php

            foreach ($beboerliste as $beboer) {
                /* @var \intern3\Beboer $beboer */
                ?>
                <tr id="<?php echo $beboer->getId(); ?>">
                    <td><?php echo $beboer->getFulltNavn(); ?></td>
                    <td><?php echo $beboer->getEpost(); ?></td>
                    <td class=sing-alle">?</td>
                    <td class=sing-slarv">?</td>
                    <td class=sing-jenter">?</td>
                    <td class=sing-gutter">?</td>
                    <td>
                        <button class="btn btn-danger" onclick="sjekk('<?php echo $beboer->getId(); ?>')">Sjekk</button>
                    </td>
                </tr>

                <?php

            }


            ?>
            </tbody>
        </table>

    </div>
</div>

<?php

require_once('bunn.php');

?>
