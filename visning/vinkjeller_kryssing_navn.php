<?php
require_once ('topp_vinkjeller.php');
require_once('topp.php');
//Denne brukes av samtlige. Pæss over vinListe som en liste med vin-objekter også funkære.
?>
<div class="container">

    <h1>Vinkjeller » Velg vin</h1>
    <hr>
    <button class="btn btn-primary btn-block" onclick="javascript:history.back();">Tilbake</button>
    <br/>
    <table id="tabellen" class="table table-responsive table-bordered">
        <thead>
        <tr>
            <td data-sortable="true">Navn</td>
            <td data-sortable="true">Type</td>
            <td data-sortable="true">Pris</td>
            <td data-sortable="true">Antall</td>
        </tr>

        </thead>

        <tbody>
        <?php
        foreach($vinListe as $vinen){
            /* @var $vinen \intern3\Vin */
            if($vinen->getAntall() < 1){
                continue;
            }

            ?>
            <tr>
                <td><a href="?a=vinkjeller/kryssing/<?php echo $vinen->getId();?>"><?php echo $vinen->getNavn();?></a></td>
                <td><?php echo $vinen->getType()->getNavn(); ?></td>
                <td><?php echo round($vinen->getPris() * $vinen->getAvanse(),2); ?></td>
                <td><?php echo round($vinen->getAntall(),0); ?></td>
            </tr>


        <?php
        }
        ?>
        </tbody>
    </table>
    <br/>
    <button class="btn btn-primary btn-block" onclick="javascript:history.back();">Tilbake</button>
</div>
<link rel="stylesheet" type="text/css" href="css/dataTables.css"/>
<script type="text/javascript" src="js/dataTables.js"></script>
<script>
    $(document).ready(function () {
        var table = $('#tabellen').DataTable({
            "paging": false,
            "searching": false
        });
    });

</script>


<?php
require_once('bunn.php');
?>
