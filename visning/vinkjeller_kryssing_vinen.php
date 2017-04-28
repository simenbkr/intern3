<?php
require_once ('topp_vinkjeller.php');
require_once('topp.php');
?>
<div class="container">

    <h1>Vinkjeller » Kryss <?php echo $vinen->getNavn();?></h1>
    <hr>
<table class="table table-bordered table-responsive">

    <tr>
        <td><?php echo $vinen->getNavn(); ?></td>
        <td><?php echo $vinen->getPris(); ?></td>
        <td><?php echo '' ?></td>
    </tr>

</table>

    <table class="table table-bordered table-responsive">
        <tr>
        <td>Navn</td>
        <td>Er med</td>
        </tr>

        <?php

        foreach($beboerListe as $beboer){ ?>

        <tr>
            <td><?php echo $beboer->getFulltNavn(); ?></td>
            <td>ting</td>
        </tr>
        <?php
        }
        ?>

    </table>


</div>
<?php
require_once('bunn.php');
?>
