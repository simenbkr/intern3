<?php
require_once('topp_utvalg.php');

?>
<script>
    function sett_fakturert() {
        $.ajax({
            type: 'POST',
            url: '?a=utvalg/vaktsjef/krysserapport',
            data: 'settfakturert=1',
            method: 'POST',
            success: function (html) {
                $(".container").replaceWith($('.container', $(html)));
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
    <h4>Sett alle til fakturert: (dette kan ta opp til 10s)</h4> <input class="btn btn-primary" type="submit" value="Nullstill" onclick="sett_fakturert()"> (Ingen vei tilbake etter at du har trykket!)
    <br/>
    <table class="table table-bordered table-responsive">

        <tr>
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
    </table>


</div>

<?php
require_once('bunn.php');
?>
