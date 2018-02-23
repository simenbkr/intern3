<?php
require_once ('topp.php');
?>
    <script>
        function fakturer() {
            $.ajax({
                type: 'POST',
                url: '?a=kjeller/lister/beboere_vin',
                data: 'fakturer=1',
                method: 'POST',
                success: function (html) {
                    location.reload();
                },
                error: function (req, stat, err) {
                    alert(err);
                }
            });
        }
    </script>
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
        [ Fakturer ]
        [ <a href="<?php echo $cd->getBase(); ?>kjeller/oversikt">Oversikt</a> ]
        [ <a href="<?php echo $cd->getBase(); ?>kjeller/regler">Regler</a> ]
    </p>
    <hr>
    <?php require_once ('tilbakemelding.php'); ?>

    <?php /*<b>Dette er IKKE-fakturerte!</b> <button class="btn btn-danger btn-sm" onclick="fakturer()">FAKTURER PERIODE</button><br/><br/> */?>

    <h4>Sett alle til fakturert: (dette kan ta opp til 10s)</h4>  (Ingen vei tilbake etter at du har trykket!)
    <p><input type="button" class="btn btn-md btn-danger" value="Nullstill" data-toggle="modal" data-target="#modal-nullstill"></p>
    <div class="modal fade" id="modal-nullstill" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Ønsker du å nullstille vin-tabellen? Husket å skrive ut?</h4>
                </div>
                <div class="modal-body">
                    <button type="button" class="btn btn-md btn-danger" onclick="fakturer()">Ja!</button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Lukk</button>
                </div>
            </div>
        </div>
    </div>


    <a href="?a=kjeller/lister/rapport">Husk å skrive ut denne før du markerer perioden som fakturert!</a>
    <table class="table table-responsive table-bordered">
        <tr>
            <th>Beboer</th>
            <th>Vin</th>
            <th>Antall</th>
            <th>Kostnad (ink mva)</th>
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
            if($vin_krysset['aktuell_vin'] == null || round($vin_krysset['aktuell_vin'],2) <= 0){
                continue;
            }
            $antall += $vin_krysset['antall'];
            $sum += $vin_krysset['kostnad'];
            ?>
            <tr>
                <td></td>
                <td><?php echo ($vin_krysset['aktuell_vin']) != null ? $vin_krysset['aktuell_vin']->getNavn() : '';?></td>
                <td><?php echo round($vin_krysset['antall'],2);?></td>
                <td><?php echo round($vin_krysset['kostnad'],2);?></td>
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
            <td><?php echo round($antall,2);?></td>
            <td><?php echo round($sum,2);?></td>
        </tr>
</table>
</div>
<?php
require_once ('bunn.php');
?>