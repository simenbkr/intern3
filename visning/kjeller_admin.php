<?php
require_once ('topp.php');
?>
<div class="container">
<h1>Kjellermester » Vinadministrasjon</h1>
    <hr>
    <table class="table table-bordered table-responsive">
        <tr>
            <th>Navn</th>
            <th>Pris</th>
            <th>Antall</th>
            <th>Type</th>
            <th>Bilde</th>
        </tr>

        <?php
        foreach($vinene as $vin){
            if($vin == null || !isset($vin)){
                continue;
            }
            ?>
            <tr>
                <td><a href="?a=kjeller/admin/<?php echo $vin->getId();?>"><?php echo $vin->getNavn();?></a></td>
                <td><?php echo $vin->getPris();?></td>
                <td><?php echo $vin->getAntall();?></td>
                <td><?php echo $vin->getType()->getNavn();?></td>
                <td><?php if(strlen($vin->getBilde()) > 0) {?><img height="25px" src="vinbilder/<?php echo $vin->getBilde();?>"><?php } ?></td>
            </tr>
            <?php
        } ?>

</div>
<?php
require_once ('bunn.php');
?>