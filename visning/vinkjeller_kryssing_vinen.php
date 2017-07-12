<?php
require_once('topp_vinkjeller.php');
require_once('topp.php');
?>
<div class="container">

    <h1>Vinkjeller » Kryss <?php echo $vinen->getNavn(); ?></h1>
    <hr>
    <div class="col-lg-6">
    <table class="table table-responsive">

        <tr>
            <td><?php echo $vinen->getNavn(); ?></td>
            <td><?php echo $vinen->getPris() * $vinen->getAvanse(); ?></td>
            <td>
                <button class="btn btn-primary" onclick="videre()">Velg</button>
            </td>
        </tr>

    </table>
</div>
    <hr>
    <br/>
    <table class="table table-bordered table-responsive">
        <tr>
            <td>Navn</td>
        </tr>

        <?php

        foreach ($beboerListe as $beboer) {
            /* @var intern3\Beboer $beboer */
            ?>

            <tr id="<?php echo $beboer->getId(); ?>" onclick="select(<?php echo $beboer->getId(); ?>)">
                <td><?php echo $beboer->getFulltNavn(); ?></td>
            </tr>
            <?php
        }
        ?>

    </table>


</div>

<script>

    var ids = [];
    function select(id) {

        var x = document.getElementById(id);
        if (ids.indexOf(id) == -1) {
            x.style.backgroundColor = "green";
            ids.push(id);
        }
        else {
            var indeks = ids.indexOf(id);
            ids.splice(indeks, 1);
            x.style.backgroundColor = "white";
        }
    }

    function videre() {
        var idString = ids.join('/');
        $.ajax({
            type: 'POST',
            url: '?a=vinkjeller/kryssing/<?php echo $vinen->getId();?>/' + idString,
            method: 'GET',
            success: function (html) {
                window.location.href = '?a=vinkjeller/kryssing/<?php echo $vinen->getId();?>/' + idString;
            },
            error: function (req, stat, err) {
                alert(err);
            }
        });
    }


</script>

<?php
require_once('bunn.php');
?>
