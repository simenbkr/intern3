<?php
require_once ('topp_vinkjeller.php');
require_once('topp.php');
?>

<div class="container">

    <h1>Vinkjeller » Velg vin</h1>
    <hr>
    <button class="btn btn-primary btn-block" onclick="javascript:history.back();">Tilbake</button>
    <br/>
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

    </table>
    <br/>
    <button class="btn btn-primary btn-block" onclick="javascript:history.back();">Tilbake</button>
</div>

<?php
require_once('bunn.php');
?>
