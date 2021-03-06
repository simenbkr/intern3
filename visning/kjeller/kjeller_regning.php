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
    function submitForm(){
        var beboerfelt = document.getElementById("beboer");
        var beboerid = beboerfelt.options[beboerfelt.selectedIndex].value;

        var vinfelt = document.getElementById("vin");
        var vinid = vinfelt.options[vinfelt.selectedIndex].value;

        var antall_felt = document.getElementById("antall");
        var antall = antall_felt.value;

        var datofelt = document.getElementById("datepicker");
        var dato = datofelt.value;

        $.ajax({
            type: 'POST',
            url: '?a=kjeller/regning',
            data: 'beboer=' + beboerid + "&vin=" + vinid + "&antall=" + antall + "&dato=" + dato,
            method: 'POST',
            success: function (data) {
                $(".tilbakemelding").replaceWith($('.tilbakemelding', $(data)));
                //$("#antall").replaceWith($('#antall', $(data)));
                //$("#datepicker").replaceWith($('#datepicker', $(data)));
                //$(".letsgo").replaceWith($('.letsgo', $(data)));
                document.getElementById("antall").value = "";
                document.getElementById("datepicker").value = "<?php echo date('Y-m-d H:i:s');?>";
            },
            error: function (req, stat, err) {
                alert(err);
            }
        });

    }

</script>
<div class="container">
    <h1>Kjellermester » Registrer regning</h1>
    <p>[ <a href="<?php echo $cd->getBase(); ?>kjeller/admin">Vinadministrasjon</a> ]
        [ <a href="<?php echo $cd->getBase(); ?>kjeller/slettet_vin">Slettet vin</a> ]
        [ <a href="<?php echo $cd->getBase(); ?>kjeller/leggtil">Legg til vin</a> ]
        [ <a href="<?php echo $cd->getBase(); ?>kjeller/add_type">Vintyper</a> ]
        [ <a href="<?php echo $cd->getBase(); ?>kjeller/pafyll">Påfyll</a> ]
        [ <a href="<?php echo $cd->getBase(); ?>kjeller/lister">Lister</a> ]
        [ Regning] [ <a href="<?php echo $cd->getBase(); ?>kjeller/svinn">Svinn</a> ]
        [ <a href="<?php echo $cd->getBase(); ?>kjeller/svinn_oversikt">Registrert svinn</a> ]
        [ <a href="<?php echo $cd->getBase(); ?>kjeller/lister/beboere_vin">Fakturer</a> ]
        [ <a href="<?php echo $cd->getBase(); ?>kjeller/oversikt">Oversikt</a> ]
    </p>
    <hr>
    <div class="col-md-12">
        <?php include(__DIR__ . '/../static/tilbakemelding.php'); ?>
        </div>
        <form onSubmit="submitForm(); return false;" action="javascript:void(0);">
            <table class="table table-bordered table-responsive">
                <tr>
                    <td>Beboer:</td>
                    <td><select id="beboer" name="beboer" class="form-control">
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
                    <td><select id="vin" name="vin" class="form-control">
                            <?php
                            foreach($vinene as $vinen){
                                if($vinen->getAntall() < 1 || $vinen->erSlettet()){
                                    continue;
                                }
                                ?>
                                <option value="<?php echo $vinen->getId();?>"><?php echo $vinen->getNavn();?></option>
                                <?php }
                            ?>
                        </select></td>
                </tr>
                <div class="letsgo">
                <tr>
                    <td>Antall: (Tall, ikke operasjoner. F.eks IKKE 1/3, men 0.33)</td>
                    <td><input id="antall" class="form-control" type="text" name="antall"></td>
                </tr>
                <tr>
                    <td>Dato:</td>
                    <td>
                        <input type="text" class="form-control" name="dato" id="datepicker" value="<?php echo date('Y-m-d H:i:s');?>"/>
                    </td>
                </tr>
                </div>
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