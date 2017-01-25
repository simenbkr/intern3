<link rel="stylesheet" type="text/css" href="css/gammelt_stilark.css"/>
<div class="container">
    <table id="beboerlistetop">
        <tr>
            <th class="left">Beboere vin</th>
            <th class="center">Singsaker Studenterhjem</th>
            <th class="right">Utskriftsdato: <?php echo date('Y-m-d');?></th>
        </tr>
    </table>
    <table class="table table-responsive table-bordered" id="beboerliste">
        <tr>
            <th class="heading">Beboer</th>
            <th class="heading">Vin</th>
            <th class="heading">Antall</th>
            <th class="heading">Kostnad</th>
        </tr>
        <?php
        $antall = 0;
        $sum = 0;
        foreach ($beboer_vin as $beboeren_med_vin) {
            $beboeren = $beboeren_med_vin['beboer'];
            $vin = $beboeren_med_vin['vin'];
            if ($beboeren == null || $vin == 0 || count($vin) < 1) {
                continue;
            } ?>
            <tr>
                <td><b><?php echo $beboeren->getFulltNavn(); ?></b></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <?php
            foreach ($vin as $vin_krysset) {
                if($vin_krysset['aktuell_vin'] == null){
                    continue;
                }
                $antall += $vin_krysset['antall'];
                $sum += $vin_krysset['kostnad'];
                ?>
                <tr>
                    <td></td>
                    <td><?php echo $vin_krysset['aktuell_vin']->getNavn(); ?></td>
                    <td><?php echo $vin_krysset['antall']; ?></td>
                    <td><?php echo $vin_krysset['kostnad']; ?></td>
                </tr>
                <?php

            }
            ?>
            <?php
        }
        ?>
        <tr>
            <td><h2>TOTALT</h2></td>
            <td></td>
            <td><?php echo $antall;?></td>
            <td><?php echo $sum;?></td>
        </tr>
    </table>