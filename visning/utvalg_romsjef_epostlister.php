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

    function del(id, group) {
        $.ajax({
            url: '?a=utvalg/romsjef/epost/' + group + "/" + id,
            type: 'DELETE',
            success: function(result) {
                $("#" + id + " ." + group.split("@"[0])).html(result);
            }
        });
    }


</script>
<div class="container">
    <h1>Utvalget &raquo; Romsjef &raquo; Epostlister</h1>
    <p> Her kan du legge til/fjerne folk fra e-postlister. Beboerenes status lastes ikke automagisk, da det tar jævlig
        lang tid fordi Google har et tregt API.
    </p>
    <p>
        NB: Når folk flyttes ut, fjernes de fra alle disse epostlistene
        og legges inn på veteranlista automagisk.
    </p>
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
