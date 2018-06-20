<?php
require_once(__DIR__ . '/../static/topp.php');
?>
    <link rel="stylesheet" type="text/css" href="css/dataTables.css"/>
    <script type="text/javascript" src="js/dataTables.js"></script>
    <script>
        function unslett(id) {
            $.ajax({
                type: 'POST',
                url: '?a=kjeller/admin/' + id,
                data: 'unslett=' + id,
                method: 'POST',
                success: function (html) {
                    document.getElementById(id).remove();
                    //$(".container").replaceWith($('.container', $(html)));
                    //$('#oppgave_' + id).html(data);
                    //location.reload();
                },
                error: function (req, stat, err) {
                    //alert(err);
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

        $(document).ready(function(){
            $('#tabellen').DataTable({
                "paging": false,
                "searching": false
            });

        });
    </script>
    <div class="container">
        <h1>Kjellermester » Vinadministrasjon</h1>
        <p>[ <a href="<?php echo $cd->getBase(); ?>kjeller/admin">Vinadministrasjon</a> ]
            [ Slettet vin ]
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
    
        <?php include(__DIR__ . '/../static/tilbakemelding.php'); ?>

        <table id="tabellen" class="table table-bordered table-responsive">
            <thead>
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
            </thead>
            <tbody>
            <?php
            foreach($vinene as $vin){
                if($vin == null || !isset($vin) || !$vin->erSlettet() || $vin->getType() == null){
                    continue;
                }
                ?>
                <tr id="<?php echo $vin->getId();?>">
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
            </tbody>
            </table>
    </div>
<?php
require_once(__DIR__ . '/../static/bunn.php');
?>