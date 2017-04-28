<?php
require_once ('topp_vinkjeller.php');
require_once('topp.php');
?>

<div class="container">

    <h1>Vinkjeller » Velg vin</h1>
    <hr>
    <table class="table table-responsive table-bordered">

        <tr>
            <td>Navn</td>
            <td>Type</td>
            <td>Pris</td>
            <td>Antall</td>
        </tr>


        <?php
        foreach($vinListe as $vinen){

            /* @var $vinen \intern3\Vin */
            ?>
            <tr>
                <td><a href="?a=vinkjeller/kryssing/<?php echo $vinen->getId();?>"><?php echo $vinen->getNavn();?></a></td>
                <td><?php echo $vinen->getType()->getNavn(); ?></td>
                <td><?php echo $vinen->getPris(); ?></td>
                <td><?php echo $vinen->getAntall(); ?></td>
            </tr>


        <?php
        }






        ?>


    </table>


</div>



<?php
require_once('bunn.php');
?>
