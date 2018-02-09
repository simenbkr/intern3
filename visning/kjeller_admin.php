<?php
require_once ('topp.php');
?>
<div class="container">
    <script>
        function slett(id) {
            $.ajax({
                type: 'POST',
                url: '?a=kjeller/admin',
                data: 'slett=' + id,
                method: 'POST',
                success: function (html) {
                    //$("#tabellen").replaceWith($('#tabellen', $(html)));
                    document.getElementById(id).remove();
                    //$('#oppgave_' + id).html(data);
                    //location.reload();
                },
                error: function (req, stat, err) {
                    alert(err);
                }
            });
        }

        $(document).ready(function(){
            $('#tabellen').DataTable({
                "paging": false,
                "searching": false
            });

        });
    </script>
<h1>Kjellermester » Vinadministrasjon</h1>
    <p>[ Vinadministrasjon ]
        [ <a href="<?php echo $cd->getBase(); ?>kjeller/slettet_vin">Slettet vin</a> ]
        [ <a href="<?php echo $cd->getBase(); ?>kjeller/leggtil">Legg til vin</a> ]
        [ <a href="<?php echo $cd->getBase(); ?>kjeller/add_type">Vintyper</a> ]
        [ <a href="<?php echo $cd->getBase(); ?>kjeller/pafyll">Påfyll</a> ]
        [<a href="<?php echo $cd->getBase(); ?>kjeller/lister">Lister</a> ]
        [ <a href="<?php echo $cd->getBase(); ?>kjeller/regning">Regning</a> ]
        [ <a href="<?php echo $cd->getBase(); ?>kjeller/svinn">Svinn</a> ]
        [ <a href="<?php echo $cd->getBase(); ?>kjeller/lister/beboere_vin">Fakturer</a> ]
        [ <a href="<?php echo $cd->getBase(); ?>kjeller/oversikt">Oversikt</a> ]
    </p>
    <hr>
    <table id="tabellen" class="table table-bordered table-responsive" data-toggle="table">
        <thead>
            <th data-sortable="true">Navn</th>
            <th data-sortable="true">Pris (innkjøp)</th>
            <th data-sortable="true">Avanse</th>
            <th data-sortable="true">Pris (beboere)</th>
            <th data-sortable="true">Antall</th>
            <th data-sortable="true">Svinn</th>
            <th data-sortable="true">Type</th>
            <th data-sortable="true">Land</th>
            <th data-sortable="true">Beskrivelse</th>
            <th data-sortable="true">Bilde</th>
            <th data-sortable="false"></th>
        </thead>
        <tbody>
        <?php
        foreach($vinene as $vin){
            if($vin == null || !isset($vin) || $vin->erSlettet() || $vin->getType() == null){
                continue;
            }
            ?>
            <tr id="<?php echo $vin->getId();?>">
                <td><a href="?a=kjeller/admin/<?php echo $vin->getId();?>"><?php echo $vin->getNavn();?></a></td>
                <td><?php echo round($vin->getPris(),2);?></td>
                <td><?php echo round($vin->getAvanse(),2); ?></td>
                <td><?php echo round($vin->getPris()*$vin->getAvanse(),2);?></td>
                <td><?php echo round($vin->getAntall(),2);?></td>
                <td>
                    <?php echo $vin->getSvinn();?>
                </td>

                <td><a href="?a=kjeller/add_type/<?php echo $vin->getType()->getId();?>"><?php echo $vin->getType()->getNavn();?></a></td>
                <td><?php echo $vin->getLand(); ?></td>
                <td><?php echo $vin->getBeskrivelse(); ?></td>
                <td><?php if(strlen($vin->getBilde()) > 0) {?><img height="25px" src="vinbilder/<?php echo $vin->getBilde();?>"><?php } ?></td>
                <td><button class="btn btn-danger btn-sm" onclick="slett(<?php echo $vin->getId();?>)">Slett</button></td>
            </tr>
            <?php
        } ?>
        </tbody>
        </table>
    <link rel="stylesheet" type="text/css" href="css/dataTables.css"/>
    <script type="text/javascript" src="js/dataTables.js"></script>
</div>
<?php
require_once ('bunn.php');
?>