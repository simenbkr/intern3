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

    body {
      background-color: #444341;
      color: #FFF;
    }
    table {
      color: #000;
    }
    /*tr:nth-child(even)
    {
      background-color: #504f4d !important;
      color:white;
    }
    tr:nth-child(odd)
    {
      background-color: #444341 !important;
      color:white;
    }*/
</style>
<div class="container">

    <h1>Vinkjeller » Kryss <?php echo $vinen->getNavn(); ?></h1>
    <hr>
    <div class="col-lg-12">
      <div class="col-lg-2">
        <?php if($vinen->getBilde() != null && $vinen->getBilde() != ""){ ?>
        <img src="vinbilder/<?php echo $vinen->getBilde(); ?>">
        <?php } else { ?>
        <img src="vinbilder/ayy.jpg">
        <?php } ?>
      </div>
      <div class="col-lg-3">
        Navn: <?php echo $vinen->getNavn(); ?><br/>
        Land: <?php echo $vinen->getLand(); ?><br/>
        Beskrivelse: <br/><?php echo $vinen->getBeskrivelse(); ?>
      </div>
      <div class="col-lg-3">
        Pris: <?php echo round($vinen->getPris() * $vinen->getAvanse(), 2); ?> kr <br/>
        Antall: <?php echo $vinen->getAntall(); ?>
      </div>

      <div class="col-lg-2">
        <button class="btn btn-primary" onclick="videre()">Velg</button>
      </div>
    </div>
    <br/><br/><br/><br/><br/><hr>
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
