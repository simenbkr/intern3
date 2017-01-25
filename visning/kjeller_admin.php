<?php
require_once ('topp.php');
?>
    <script>
        function slett(id) {
            $.ajax({
                type: 'POST',
                url: '?a=kjeller/admin',
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
    <p>[ Vinadministrasjon ] [ <a href="<?php echo $cd->getBase(); ?>kjeller/leggtil">Legg til vin</a> ] [ <a href="<?php echo $cd->getBase(); ?>kjeller/add_type">Vintyper</a> ]
        [ <a href="<?php echo $cd->getBase(); ?>kjeller/pafyll">Påfyll</a> ] [ <a href="<?php echo $cd->getBase(); ?>kjeller/lister">Lister</a> ]
        [ <a href="<?php echo $cd->getBase(); ?>kjeller/regning">Regning</a> ] [ <a href="<?php echo $cd->getBase(); ?>kjeller/svinn">Svinn</a> ]</p>
    <hr>
    <table class="table table-bordered table-responsive">
        <tr>
            <th>Navn</th>
            <th>Pris</th>
            <th>Antall</th>
            <th>Svinn</th>
            <th>Type</th>
            <th>Bilde</th>
            <th></th>
        </tr>

        <?php
        foreach($vinene as $vin){
            if($vin == null || !isset($vin)){
                continue;
            }
            ?>
            <tr>
                <td><a href="?a=kjeller/admin/<?php echo $vin->getId();?>"><?php echo $vin->getNavn();?></a></td>
                <td><?php echo round($vin->getPris(),2);?></td>
                <td><?php echo $vin->getAntall();?></td>
                <td>
                    <?php echo $vin->getSvinn();?>
                </td>

                <td><a href="?a=kjeller/add_type/<?php echo $vin->getType()->getId();?>"><?php echo $vin->getType()->getNavn();?></a></td>
                <td><?php if(strlen($vin->getBilde()) > 0) {?><img height="25px" src="vinbilder/<?php echo $vin->getBilde();?>"><?php } ?></td>
                <td><button class="btn btn-danger btn-sm" onclick="slett(<?php echo $vin->getId();?>)">Slett</button></td>
            </tr>
            <?php
        } ?>

</div>
<?php
require_once ('bunn.php');
?>