<?php
require_once ('topp.php');
?>
<script>
    function unslett(id){
        $.ajax({
            type: 'POST',
            url: '?a=kjeller/admin/' + id,
            data: 'unslett=' + id,
            method: 'POST',
            success: function (data) {
                //location.reload();
            },
            error: function (req, stat, err) {
                alert(err);
            }
        });
    }
</script>
<div class="container">
    <h1>Kjellermester » Vinadministrasjon » Endre vin</h1>
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
    </p>
    <hr>

    <?php require_once ('tilbakemelding.php'); ?>

    <form action="" method="post" enctype="multipart/form-data" onsubmit="">
        <table class="table table-bordered table-responsive">
            <tr>
                <td>Navn:</td>
                <td><input type="text" class="form-control" name="navn" value="<?php echo $vinen->getNavn(); ?>"></td>
            </tr>
            <tr>
                <td>Pris:</td>
                <td><input type="text" class="form-control" name="pris" value="<?php echo $vinen->getPris(); ?>"></td>
            </tr>
            <tr>
                <td>Avanse:</td>
                <td><input type="text" class="form-control" name="avanse" value="<?php echo $vinen->getAvanse();?>"></td>
            </tr>
            <tr>
                <td>Antall i beholdning:</td>
                <?php /*<td><input type="text" name="antall" value="<?php echo $vinen->getAntall();?>"></td>*/?>
                <td><?php echo round($vinen->getAntall(),0);?></td>
            </tr>
            <tr>
                <td>Type:</td>
                <td><select name="type" class="form-control">
                        <?php
                        foreach($vintyper as $vintypen){
                            ?>
                            <option value="<?php echo $vintypen->getId();?>"<?php
                            if($vinen->getTypeId() == $vintypen->getId()){
                                echo 'selected=\"selected\"';
                            }
                            ?>><?php echo $vintypen->getNavn();?></option>
                            <?php
                        }
                        ?>
                    </select></td>
            </tr>
            <tr>
                <td>Land</td>
                <td><input type="text" class="form-control" name="land" value="<?php echo $vinen->getLand(); ?>"></td>
            </tr>
            <tr>
                <td>Beskrivelse</td>
                <td><textarea name="beskrivelse" class="form-control"><?php echo $vinen->getBeskrivelse();?></textarea></td>
            </tr>
            <tr>
                <td>Bilde:</td>
                <td><input type="file" name="image" /><img height="200px" <?php if($vinen->getBilde()) { ?> src="vinbilder/<?php echo $vinen->getBilde();?><?php } ?>"</td>
            </tr>
            <tr>
                <td></td>
                <td><input class="btn btn-primary" type="submit" value="Endre" name="endre"></td>
            </tr>
            <?php if($vinen->erSlettet()){ ?>
            <tr>
                <td></td>
                <td><input class="btn btn-warning" type="button" onclick="unslett(<?php echo $vinen->getId();?>)" value="Unslett"></td>
            </tr>
            <?php } ?>
        </table>
    </form>

</div>
<?php
require_once ('bunn.php');
?>
