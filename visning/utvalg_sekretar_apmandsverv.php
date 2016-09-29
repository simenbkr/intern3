<?php

require_once('topp_utvalg.php');

?>
<script>
    function fjern(beboerid,vervid) {
        $.ajax({
            type: 'POST',
            url: '?a=utvalg/sekretar/apmandsverv',
            data: 'fjern=' + beboerid+'&verv='+vervid,
            method: 'POST',
            success: function (data) {
                location.reload();
            },
            error: function (req, stat, err) {
                alert(err);
            }
        });
    }


</script>
<div class="col-md-12">
    <h1>Utvalget &raquo; Sekretær &raquo; Åpmandsverv</h1>


    <p></p>

</div>

<div class="col-md-12">
    <table class="table table-bordered">
        <tr>
            <th>Åpmandsverv</th>
            <th>Åpmand/åpmend</th>
            <th>Legg til/endre</th>
            <th>Epost</th>
        </tr>
        <?php

        foreach ($vervListe as $verv) {
        ?>
        <div id="<?php echo $verv->getId();?>">
        <tr id="<?php echo $verv->getId();?>">
            <td><?php echo $verv->getNavn(); ?></td>
            <td><?php
                $i = 0;
                foreach ($verv->getApmend() as $apmand) {
                    if ($i++ > 0) {
                        echo ', ';
                    }
                    echo '<a href="?a=beboer/' . $apmand->getId() . '">' . $apmand->getFulltNavn() . '</a>';?> <button onclick="fjern(<?php echo $apmand->getId(); ?>,<?php echo $verv->getId(); ?>)">&#x2718;</button>
                <?php } ?>
                </td>
            <td></div>
                <form action="" method="POST">
                <select id="vervet" name="vervet" onchange="this.form.submit()">
                    <option value="0">- velg -</option>

                    <?php
                    foreach (intern3\BeboerListe::utenVervId($verv->getId()) as $beboer) {
                        ?>

                        <option id="vervet" value="<?php echo $beboer->getId() . '&' . $verv->getId(); ?>" name="<?php echo $beboer->getId() . '&' . $verv->getId(); ?>">
                            <?php echo $beboer->getFulltNavn(); ?>
                        </option>
                        <?php
                    }
                    ?>
                </select>
                <noscript><input type="submit" value="Submit"></noscript>
            </form>
            </td>
            <td><?php
                $epost = $verv->getEpost();
                if ($verv == null) {
                    echo ' ';
                } else {
                    echo '<a href="mailto:' . $epost . '">' . $epost . '</a>';
                }
                ?></td>
            <?php
            }
            ?>
        </tr>


        <!-- <input type="button" class="btn btn-sm btn-info" value="Endre"> -->

</div>

</table>
<?php

require_once('bunn.php');

?>
