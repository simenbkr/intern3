<?php
require_once('topp_vinkjeller.php');
require_once('topp.php');
/* @var \intern3\Vin $vinen */
?>
<link rel="stylesheet" type="text/css" href="css/dataTables.css"/>
<script type="text/javascript" src="js/dataTables.js"></script>
<style>
    table.tableSection {
        display: table;
        width: 100%;
    }

    table.tableSection thead,
    table.tableSection tbody {
        width: 100%;
    }

    table.tableSection thead {
        overflow-y: scroll;
        display: table;
        table-layout: fixed;
        width: calc(100% - 17px); /* assuming scrollbar width as 16px */
    }

    table.tableSection tbody {
        overflow: auto;
        height: 600px;
        display: block;
    }

    table.tableSection tr {
        width: 100%;
        text-align: left;
        display: table;
        table-layout: fixed;
    }
</style>
<div class="container">

    <h1>Vinkjeller » Kryss <?php echo $vinen->getNavn(); ?></h1>
    <hr>
    <div class="col-lg-12">
    <table class="table table-bordered table-responsive">

        <tr>
            <td><?php echo $vinen->getNavn(); ?></td>
            <td><?php echo round($vinen->getPris() * $vinen->getAvanse(), 2); ?>kr/enhet</td>
            <td><?php echo $vinen->getBeskrivelse(); ?></td>

            <?php if($vinen->getBilde() != null && $vinen->getBilde() != ""){ ?>
            <td><img src="vinbilder/<?php echo $vinen->getBilde(); ?>"</td>
            <?php } ?>

            <td>
                <button class="btn btn-primary" onclick="videre()">Velg</button>
            </td>
        </tr>

    </table>
</div>
    <hr>
    <br/>

    <button class="btn btn-primary btn-block" onclick="javascript:history.back();">Tilbake</button>
    <br/>


    <table id="tabellen" data-togle="table" class="table table-bordered table-responsive tableSection">
        <thead>
        <tr>
            <td>Navn</td>
        </tr>
        </thead>

        <tbody>
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
        </tbody>
    </table>
    <p>Trykk på navn for å velge.</p>


</div>

<script>

    $(document).ready(function () {
        $('#tabellen').DataTable({
            "paging": false,
            "searching": false
        });

    });

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
