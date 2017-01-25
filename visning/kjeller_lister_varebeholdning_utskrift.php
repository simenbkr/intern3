<link rel="stylesheet" type="text/css" href="css/gammelt_stilark.css"/>
<div class="col-md-12">
    <table id="beboerlistetop">
        <tr>
            <th class="left">Varebeholdning</th>
            <th class="center">Singsaker Studenterhjem</th>
            <th class="right">Utskriftsdato: <?php echo date('Y-m-d');?></th>
        </tr>
    </table>
    <table class="table table-bordered table-responsive" id="beboerliste">
        <tr>
            <th class="heading">Navn</th>
            <th class="heading">Antall</th>
            <th class="heading">Pris</th>
            <th class="heading">Type</th>
        </tr>
        <?php
        foreach($vinene as $vin) {
            if($vin == null || $vin->getAntall() == 0){
                continue;
            }
            ?>
        <tr>
            <td><?php echo $vin->getNavn();?></td>
            <td><?php echo $vin->getAntall();?></td>
            <td><?php echo $vin->getPris();?></td>
            <td><?php echo $vin->getType()->getNavn();?></td>
        </tr>
        <?php
        }
?>
        </table>
