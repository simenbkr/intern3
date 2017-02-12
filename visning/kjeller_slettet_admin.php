<?php
require_once ('topp.php');
?>
    <script>
        function unslett(id) {
            $.ajax({
                type: 'POST',
                url: '?a=kjeller/admin/' + id,
                data: 'unslett=' + id,
                method: 'POST',
                success: function (html) {
                    location.reload();
                    //$('#oppgave_' + id).html(data);
                    //location.reload();
                },
                error: function (req, stat, err) {
                    alert(err);
                }
            });
        }
    </script>

    <script>
        function slett(id) {
            $.ajax({
                type: 'POST',
                url: '?a=kjeller/slettet_vin',
                data: 'slett=' + id,
                method: 'POST',
                success: function (html) {
                    $(".container").replaceWith($('.container', $(html)));
                    //$('#oppgave_' + id).html(data);
                    //location.reload();
                },
                error: function (req, stat, err) {
                    alert(err);
                }
            });
        }
    </script>
    <div class="container">
        <h1>Kjellermester » Vinadministrasjon</h1>
        <p>[ <a href="<?php echo $cd->getBase(); ?>kjeller/admin">Vinadministrasjon</a> ] [ Slettet vin ] [ <a href="<?php echo $cd->getBase(); ?>kjeller/leggtil">Legg til vin</a> ] [ <a href="<?php echo $cd->getBase(); ?>kjeller/add_type">Vintyper</a> ]
            [ <a href="<?php echo $cd->getBase(); ?>kjeller/pafyll">Påfyll</a> ] [ <a href="<?php echo $cd->getBase(); ?>kjeller/lister">Lister</a> ]
            [ <a href="<?php echo $cd->getBase(); ?>kjeller/regning">Regning</a> ] [ <a href="<?php echo $cd->getBase(); ?>kjeller/svinn">Svinn</a> ] [ <a href="<?php echo $cd->getBase(); ?>kjeller/lister/beboere_vin">Fakturer</a> ]</p>
        <hr>
        <?php if(isset($tilbakemelding) && strlen($tilbakemelding) > 1){ ?>
            <div class="alert alert-danger fade in" id="success" style="display:table; margin: auto; margin-top: 5%">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <?php echo $tilbakemelding; ?>
            </div>
            <p></p>
        <?php } unset($tilbakemelding) ?>
        <table class="table table-bordered table-responsive">
            <tr>
                <th>Navn</th>
                <th>Pris (innkjøp)</th>
                <th>Avanse</th>
                <th>Pris (beboere)</th>
                <th>Antall</th>
                <th>Svinn</th>
                <th>Type</th>
                <th>Bilde</th>
                <th></th>
                <th></th>
            </tr>

            <?php
            foreach($vinene as $vin){
                if($vin == null || !isset($vin) || !$vin->erSlettet()){
                    continue;
                }
                ?>
                <tr>
                    <td><a href="?a=kjeller/admin/<?php echo $vin->getId();?>"><?php echo $vin->getNavn();?></a></td>
                    <td><?php echo round($vin->getPris(),2);?></td>
                    <td><?php echo round($vin->getAvanse(),2); ?></td>
                    <td><?php echo round($vin->getPris()*$vin->getAvanse(),2);?></td>
                    <td><?php echo $vin->getAntall();?></td>
                    <td>
                        <?php echo $vin->getSvinn();?>
                    </td>

                    <td><a href="?a=kjeller/add_type/<?php echo $vin->getType()->getId();?>"><?php echo $vin->getType()->getNavn();?></a></td>
                    <td><?php if(strlen($vin->getBilde()) > 0) {?><img height="25px" src="vinbilder/<?php echo $vin->getBilde();?>"><?php } ?></td>
                    <td><button class="btn btn-warning btn-sm" onclick="unslett(<?php echo $vin->getId();?>)">Unslett</button></td>
                    <td><button class="btn btn-danger btn-sm" onclick="slett(<?php echo $vin->getId();?>)">SLETT PERMANENT</button></td>
                </tr>
                <?php
            } ?>

    </div>
<?php
require_once ('bunn.php');
?>