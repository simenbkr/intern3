<?php

require_once('topp_utvalg.php');

?>
<script>
    function endreVakter(){
        var host_halv = document.getElementById("1h").value;
        var vaar_halv = document.getElementById("1v").value;
        var host_hel = document.getElementById("2h").value;
        var vaar_hel = document.getElementById("2v").value;
        $.ajax({
            type: 'POST',
            url: '?a=utvalg/vaktsjef/vaktoversikt',
            data: 'endreVakt=1&hosthalv=' + host_halv + "&vaarhalv=" + vaar_halv + "&hosthel=" + host_hel + "&vaarhel=" + vaar_hel,
            method: 'POST',
            success: function (data) {
                location.reload();
            },
            error: function (req, stat, err) {
                alert(err);
            }
        });
    }
</script>
<div class="col-md-6">
    <h1>Utvalget &raquo; Vaktsjef &raquo; Vaktoversikt</h1>

    <p>
    <h2>Antall vakter: <?php echo $antallVakter; ?></h2></p>
    <p>
    <h2>Ufordelte vakter: <?php echo $antallUfordelte; ?></h2></p>
    <p>
    <h2>Ubekreftede vakter: <?php echo $antallUbekreftet; ?></h2></p>

</div>
<div class="col-md-3" id="kake">
    <table class="table table-bordered table-responsive small">
        <tr>
        <th>Rolle</th>
        <th>Vakter Høst</th>
            <th>Vakter Vår</th>
        </tr>
        <?php foreach($roller as $rollen){
            if($rollen->getNavn() == "Full regi") {continue;} ?>
        <tr>
            <td><?php echo $rollen->getNavn();?></td>
            <td><input type="text" name="host" id="<?php echo $rollen->getId();?>h" value="<?php echo $rollen->getVakterH();?>" size="1"></td>
            <td><input type="text" name="vaar" id="<?php echo $rollen->getId();?>v" value="<?php echo $rollen->getVakterV();?>" size="1"></td>
            </tr>
        <?php
        }
        ?>
        <tr>
            <td></td>
            <td><button class="btn btn-primary btn-sm" onclick="endreVakter()">Endre</button></td>
            <td></td>
        </tr>
    </table>
</div>

<div class="col-md-12">

    <table class="table table-bordered">
        <tr>
            <th>Navn</th>
            <th>Straffevakter</th>
            <th>Skal sitte</th>
            <th>Har sittet</th>
            <th>Er oppsatt</th>
            <th>Førstevakter</th>
            <th>Har igjen</th>
            <th>Ikke oppsatt</th>
            <th>Ikke bekreftet</th>
        </tr>

        <?php
        foreach ($beboerListe as $beboer) {
            $bruker = $beboer->getBruker();
            ?>
            <tr>
                <td><a href="?a=beboer/<?php echo $beboer->getId(); ?>"><?php echo $beboer->getFulltNavn(); ?></a></td>
                <td><?php echo $bruker->antallStraffevakter(); ?></td>
                <td><?php echo $bruker->antallVakterSkalSitte(); ?></td>
                <td><?php echo $bruker->antallVakterHarSittet(); ?></td>
                <td><?php echo $bruker->antallVakterErOppsatt(); ?></td>
                <td><?php echo $bruker->antallForstevakter(); ?></td>
                <td><?php echo $bruker->antallVakterHarIgjen(); ?></td>
                <td><?php echo $bruker->antallVakterIkkeOppsatt(); ?></td>
                <td><?php echo $bruker->antallVakterIkkeBekreftet(); ?></td>
            </tr>
            <?php
        }

        ?>
    </table>

</div>

<?php

require_once('bunn.php');

?>
