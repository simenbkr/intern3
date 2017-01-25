<?php
require_once ('topp.php');
?>
<script>
    $(function() {
        $('#datepicker').datepicker({
            dateFormat: 'yy-mm-dd',
            onSelect: function(datetext){
                var d = new Date(); // for now
                var h = d.getHours();
                h = (h < 10) ? ("0" + h) : h ;

                var m = d.getMinutes();
                m = (m < 10) ? ("0" + m) : m ;

                var s = d.getSeconds();
                s = (s < 10) ? ("0" + s) : s ;

                datetext = datetext + " " + h + ":" + m + ":" + s;
                $('#datepicker').val(datetext);
            },
        });
    });
</script>
<div class="container">
    <h1>Kjellermester » Registrer regning</h1>
    <p>[ <a href="<?php echo $cd->getBase(); ?>kjeller/admin">Vinadministrasjon</a> ] [ <a href="<?php echo $cd->getBase(); ?>kjeller/leggtil">Legg til vin</a> ] [ <a href="<?php echo $cd->getBase(); ?>kjeller/add_type">Vintyper</a> ]
        [ <a href="<?php echo $cd->getBase(); ?>kjeller/pafyll">Påfyll</a> ] [ <a href="<?php echo $cd->getBase(); ?>kjeller/lister">Lister</a> ]
        [ Regning] [ <a href="<?php echo $cd->getBase(); ?>kjeller/svinn">Svinn</a> ] [ <a href="<?php echo $cd->getBase(); ?>kjeller/lister/beboere_vin">Fakturer</a> ]</p>
    <hr>
    <div class="col-md-12">
        <form action="" method="post" enctype="multipart/form-data">
            <table class="table table-bordered table-responsive">
                <tr>
                    <td>Beboer:</td>
                    <td><select name="beboer">
                            <?php
                            foreach ($beboerlista as $beboeren) {
                                ?>
                                <option
                                    value="<?php echo $beboeren->getId(); ?>"><?php echo $beboeren->getFulltNavn(); ?></option>
                                <?php
                            }
                            ?>
                        </select></td>
                </tr>
                <tr>
                    <td>Vin:</td>
                    <td><select name="vin">
                            <?php
                            foreach($vinene as $vinen){
                                if($vinen->getAntall() < 1){
                                    continue;
                                }
                                ?>
                                <option value="<?php echo $vinen->getId();?>"><?php echo $vinen->getNavn();?></option>
                                <?php }
                            ?>
                        </select></td>
                </tr>
                <tr>
                    <td>Antall: (Tall, ikke operasjoner. F.eks IKKE 1/3, men 0.33)</td>
                    <td><input type="text" name="antall"></td>
                </tr>
                <tr>
                    <td>Dato:</td>
                    <td>
                        <input type="text" name="dato" id="datepicker"/>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td><input class="btn btn-primary" type="submit" value="Legg til"></td>
                </tr>
            </table>
        </form>
    </div>

</div>
<?php
require_once ('bunn.php');
?>