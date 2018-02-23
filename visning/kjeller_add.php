<?php
require_once('topp.php');
?>
<div class="container">
    <h1>Kjellermester » Legg til vin</h1>
    <p>[ <a href="<?php echo $cd->getBase(); ?>kjeller/admin">Vinadministrasjon</a> ]
        [ <a href="<?php echo $cd->getBase(); ?>kjeller/slettet_vin">Slettet vin</a> ]
        [ Legg til vin ]
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
        <?php require_once ('tilbakemelding.php'); ?>
    <div class="col-md-12">
        <form action="" method="post" enctype="multipart/form-data">
            <table class="table table-bordered table-responsive">
                <tr>
                    <td>Navn:</td>
                    <td><input class="form-control" type="text" name="navn" value=""></td>
                </tr>
                <tr>
                    <td>Pris:</td>
                    <td><input class="form-control" type="text" name="pris" value="" ></td>
                </tr>
                <tr>
                    <td>Avanse:</td>
                    <td><input class="form-control" type="text" name="avanse" value=""</td>
                </tr>
                <tr>
                    <td>Antall innkjøpt:</td>
                    <td><input class="form-control" type="number" name="antall"></td>
                </tr>
                <tr>
                    <td>Type:</td>
                    <td><select name="type" class="form-control">
                            <?php
                            foreach ($vintyper as $vintypen) {
                                ?>
                                <option
                                    value="<?php echo $vintypen->getId(); ?>"><?php echo $vintypen->getNavn(); ?></option>
                                <?php
                            }
                            ?>
                        </select></td>
                </tr>
                <tr>
                    <td>Land</td>
                    <td><input class="form-control" type="text" name="land"></td>
                </tr>
                <tr>
                    <td>Beskrivelse</td>
                    <td><textarea class="form-control" ="beskrivelse" placeholder="Beskrivelse.."></textarea></td>
                </tr>
                <tr>
                    <td>Bilde:</td>
                    <td><input type="file" class="form-control" name="image"/></td>
                </tr>
                <tr>
                    <td></td>
                    <td><input class="btn btn-primary" type="submit" value="Legg til"></td>
                </tr>
            </table>
        </form>
    </div>
</div>