<?php
require_once ('topp.php');
?>
    <div class="container">
        <h1>Kjellermester » Beboere og vin</h1>
        <p>[ <a href="<?php echo $cd->getBase(); ?>kjeller/admin">Vinadministrasjon</a> ]
            [ <a href="<?php echo $cd->getBase(); ?>kjeller/slettet_vin">Slettet vin</a> ]
            [ <a href="<?php echo $cd->getBase(); ?>kjeller/leggtil">Legg til vin</a> ]
            [ <a href="<?php echo $cd->getBase(); ?>kjeller/add_type">Vintyper</a> ]
            [ <a href="<?php echo $cd->getBase(); ?>kjeller/pafyll">Påfyll</a> ]
            [ <a href="<?php echo $cd->getBase(); ?>kjeller/lister">Lister</a> ]
            [ <a href="<?php echo $cd->getBase(); ?>kjeller/regning">Regning</a> ]
            [ <a href="<?php echo $cd->getBase(); ?>kjeller/svinn">Svinn</a> ]
            [ <a href="<?php echo $cd->getBase(); ?>kjeller/lister/beboere_vin">Fakturer</a> ]
            [ <a href="<?php echo $cd->getBase(); ?>kjeller/oversikt">Oversikt</a> ]
            [ <a href="<?php echo $cd->getBase(); ?>kjeller/regler">Regler</a> ]
        </p>
        <hr>
        <b>Dette er fakturerte!</b>
        <table class="table table-responsive table-bordered">
            <tr>
                <th>Beboer</th>
                <th>Vin</th>
                <th>Antall</th>
                <th>Kostnad</th>
            </tr>
            <?php
            $antall = 0;
            $sum = 0;
            foreach($beboer_vin as $beboeren_med_vin){
                $beboeren = $beboeren_med_vin['beboer'];
                $vin = $beboeren_med_vin['vin'];
                if($beboeren == null || $vin == 0 || count($vin) < 1){
                    continue;
                } ?>
                <tr>
                    <td><b><?php echo $beboeren->getFulltNavn();?></b></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <?php
                foreach($vin as $vin_krysset){
                    if($vin_krysset['aktuell_vin'] == null){
                        continue;
                    }
                    $antall += $vin_krysset['antall'];
                    $sum += $vin_krysset['kostnad'];
                    ?>
                    <tr>
                        <td></td>
                        <td><?php echo $vin_krysset['aktuell_vin']->getNavn();?></td>
                        <td><?php echo $vin_krysset['antall'];?></td>
                        <td><?php echo $vin_krysset['kostnad'];?></td>
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
    </div>
<?php
require_once ('bunn.php');
?>