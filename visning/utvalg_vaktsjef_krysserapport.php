<?php
require_once('topp_utvalg.php');

?>

<?php /*<style> #loader {
        position: fixed;
        background-color: #FFF;
        opacity: 1;
        height: 100%;
        width: 100%;
        top: 0;
        left: 0;
        z-index: 10;
    }
</style>
<div class="container" id="loader" style="display:none">
    NULLSTILLER PERIODE. VENNLIGST VENT.
</div> */ ?>
<script>
    function sett_fakturert() {
        $("#kult").show();
        $.ajax({
            type: 'POST',
            url: '?a=utvalg/vaktsjef/krysserapport',
            data: 'settfakturert=1',
            method: 'POST',
            success: function (html) {
                $('.modal-backdrop').hide();
                $(".container").replaceWith($('.container', $(html)));
                $("#kult").hide();
            },
            error: function (req, stat, err) {
                alert(err);
            }
        });
    }
</script>
<div class="container">
    <h1>Utvalget » Vaktsjef » Krysserapport</h1>
    <h3>[ Krysserapport ] [ <a href="<?php echo $cd->getBase(); ?>utvalg/vaktsjef/krysserapportutskrift">Utskrift</a> ]
    </h3>

    <?php if (isset($periodeFakturert)) { ?>
        <div class="alert alert-success fade in" id="success" style="display:table; margin: auto; margin-top: 5%">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            Perioden ble nullstilt!
        </div>
        <p></p>
    <?php unset($periodeFakturert); } ?>

    <table class="table table-bordered table-responsive">
        <tr>
            <th class="tittel">Krysseliste</th>
            <th class="dato">
                Fra: <?php echo date('Y-m-d H:i', strtotime($sistFakturert)); ?>
                Til: <?php echo date('Y-m-d H:i'); ?>
            </th>
        </tr>
    </table>
    <?php /* <input class="btn btn-primary" type="submit" value="Nullstill" onclick="sett_fakturert()"> */?>
    <h4>Sett alle til fakturert: (dette kan ta opp til 10s)</h4>  (Ingen vei tilbake etter at du har trykket!)
    <p><input type="button" class="btn btn-md btn-danger" value="Nullstill" data-toggle="modal" data-target="#modal-nullstill"></p>
    <div class="modal fade" id="modal-nullstill" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Ønsker du å nullstille kryssetabellen? Husket å skrive ut?</h4>
                </div>
                <div class="modal-body">
                    <button type="button" class="btn btn-md btn-danger" onclick="sett_fakturert()">Ja!</button>
                    <div id="kult" style="display:none">
                        <p>Fakturer nå altså, vent litt!</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Lukk</button>
                </div>
            </div>
        </div>
    </div>
    <br/>
    <table class="table table-bordered table-responsive">

        <tr><th class="">Navn</th>
        <?php
        foreach($drikke as $drikken) {
            /*
            if($drikken->getId() == 1 || $drikken->getNavn() == 'Pant'){
                continue;
            } */
            if (($drikken->getId() == 1 || $drikken->getNavn() == 'Pant' ||
                (!$drikken->harBlittDrukketSiden($sistFakturert) && $drikken->getAktiv() == 0))){
                continue;
            }
            ?>
            <th class=""><?php echo $drikken->getNavn();?></th>
            <?php }
            ?>
            </tr>
        <?php foreach($krysseListeMonthListe as $beboerID => $krysseliste){
            $beboeren = $beboerListe[$beboerID]; ?>

        <tr>
            <td class="navn"><a href="?a=utvalg/vaktsjef/detaljkryss/<?php echo $beboeren->getId();?>"><?php echo $beboeren->getFulltNavn();?></td>
            <?php foreach($drikke as $drikken){
                /*
                if($drikken->getId() == 1 || $drikken->getNavn() == 'Pant' || $drikken->harBlittDrukketSiden($sistFakturert)){
                    continue;
                } */
                if (($drikken->getId() == 1 || $drikken->getNavn() == 'Pant' ||
                    (!$drikken->harBlittDrukketSiden($sistFakturert) && $drikken->getAktiv() == 0))){
                    continue;
                }
                ?>
                <td class="<?php echo $drikken->getNavn();?>"><?php echo $krysseliste[$drikken->getNavn()];?></td>
            <?php } ?>
        </tr>

<?php
        }

?>
<?php
        /*<tr>
            <th class="">Navn</th>
            <th class="">Øl</th>
            <th class="">Cider</th>
            <th class="">Carlsberg</th>
            <th class="">Rikdom</th>
            <th class="">Pant</th>
        </tr>
        <?php
        foreach ($krysseListeMonthListe as $beboerID => $krysseliste) {
            $beboeren = $beboerListe[$beboerID];
            ?>
            <tr>
                <td class="navn"><a
                        href="?a=utvalg/vaktsjef/detaljkryss/<?php echo $beboeren->getId(); ?>"><?php echo $beboeren->getFulltNavn(); ?></a>
                </td>
                <td class="øl"><?php echo $krysseliste['Øl']; ?></td>
                <td class="cider"><?php echo $krysseliste['Cider'] ?></td>
                <td class="carlsberg"><?php echo $krysseliste['Carlsberg']; ?></td>
                <td class="rikdom"><?php echo $krysseliste['Rikdom']; ?></td>
                <td class="pant"><?php echo $krysseliste['Pant']; ?></td>
            </tr>
            <?php
        }
        ?>
    </table> */?>


</div>

<?php
require_once('bunn.php');
?>