<?php
require_once ('topp.php');
?>
<div class="container">
<h1>Kjellermester » Påfyll</h1>
    <p>[ <a href="<?php echo $cd->getBase(); ?>kjeller/admin">Vinadministrasjon</a> ]
        [ <a href="<?php echo $cd->getBase(); ?>kjeller/slettet_vin">Slettet vin</a> ]
        [ <a href="<?php echo $cd->getBase(); ?>kjeller/leggtil">Legg til vin</a> ]
        [ <a href="<?php echo $cd->getBase(); ?>kjeller/add_type">Vintyper</a> ]
        [ Påfyll ] [ <a href="<?php echo $cd->getBase(); ?>kjeller/lister">Lister</a> ]
        [ <a href="<?php echo $cd->getBase(); ?>kjeller/regning">Regning</a> ]
        [ <a href="<?php echo $cd->getBase(); ?>kjeller/svinn">Svinn</a> ]
        [ <a href="<?php echo $cd->getBase(); ?>kjeller/lister/beboere_vin">Fakturer</a> ]
        [ <a href="<?php echo $cd->getBase(); ?>kjeller/oversikt">Oversikt</a> ]
    </p>
    <hr>

    <?php require_once ('tilbakemelding.php'); ?>

    <div class="col-md-12">
        <form action="" method="post" enctype="multipart/form-data">
            <table class="table table-bordered table-responsive">
                <tr>
                    <td>Vin:</td>
                    <td><select name="vin" class="form-control">
                            <?php
                            foreach($vinene as $vinen){
                                if($vinen == null || $vinen->erSlettet()){
                                    continue;
                                }
                                ?>
                                <option value="<?php echo $vinen->getId();?>"><?php echo $vinen->getNavn();?></option>
                            <?php }
                            ?>
                        </select></td>
                </tr>
                <tr>
                    <td>Antall:</td>
                    <td><input type="text" class="form-control" name="antall"></td>
                </tr>
                <tr>
                    <td></td>
                    <td><input class="btn btn-primary" type="submit" value="Fyll på"></td>
                </tr>
            </table>
        </form>
    </div>
</div>
<?php
require_once ('bunn.php');
?>