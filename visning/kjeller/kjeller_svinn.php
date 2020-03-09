<?php
require_once(__DIR__ . '/../static/topp.php');
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
    <h1>Kjellermester » Registrer svinn</h1>
    <p>[ <a href="<?php echo $cd->getBase(); ?>kjeller/admin">Vinadministrasjon</a> ]
        [ <a href="<?php echo $cd->getBase(); ?>kjeller/slettet_vin">Slettet vin</a> ]
        [ <a href="<?php echo $cd->getBase(); ?>kjeller/leggtil">Legg til vin</a> ]
        [ <a href="<?php echo $cd->getBase(); ?>kjeller/add_type">Vintyper</a> ]
        [ <a href="<?php echo $cd->getBase(); ?>kjeller/pafyll">Påfyll</a> ]
        [ <a href="<?php echo $cd->getBase(); ?>kjeller/lister">Lister</a> ]
        [ <a href="<?php echo $cd->getBase(); ?>kjeller/regning">Regning</a> ]
        [ Svinn ]
        [ <a href="<?php echo $cd->getBase(); ?>kjeller/svinn_oversikt">Registrert svinn</a> ]
        [ <a href="<?php echo $cd->getBase(); ?>kjeller/lister/beboere_vin">Fakturer</a> ]
        [ <a href="<?php echo $cd->getBase(); ?>kjeller/oversikt">Oversikt</a> ]
        [ <a href="<?php echo $cd->getBase(); ?>kjeller/regler">Regler</a> ]
    </p>
    <hr>
    
    <?php include(__DIR__ . '/../static/tilbakemelding.php'); ?>

    <div class="col-md-12">
        <form action="" method="post" enctype="multipart/form-data">
            <table class="table table-bordered table-responsive">
                <tr>
                <td>Vin:</td>
                    <td><select name="vin" class="form-control">
                            <?php
                            foreach ($vinene as $vin) {
                                if(!($vin->getAntall() > 0) || $vin->erSlettet()){
                                    continue;
                                }
                                ?>
                                <option
                                    value="<?php echo $vin->getId(); ?>"><?php echo $vin->getNavn(); ?></option>
                                <?php
                            }
                            ?>
                        </select></td>
                </tr>
                <tr>
                    <td>Antall borte/svinn</td>
                    <td><input type="text" name="antall" class="form-control"></td>
                </tr>
                <tr>
                    <td>Dato (ca når skjedde svinnet?):</td>
                    <td>
                        <input type="text" name="dato" id="datepicker" class="form-control"/>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td><input class="btn btn-danger" type="submit" value="Registrer"></td>
                </tr>
            </table>
        </form>
    </div>
</div>
<?php
require_once(__DIR__ . '/../static/bunn.php');
?>