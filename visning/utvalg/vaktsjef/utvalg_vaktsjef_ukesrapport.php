<?php
require_once(__DIR__ . '/../topp_utvalg.php');

?>
    <script>
        var ukenr = 1;
        var aaret = 2017;
        function velgUke(nr) {

            $.ajax({
                url: "?a=utvalg/vaktsjef/ukerapport_tabell/" + nr + '/' + aaret,
                type: 'GET',
                success: function(html){
                    $('#kryss').html(html);
                },
                error: function(req,stat,err){
                    alert(err);
                }
            });
            ukenr = nr;
        }

        function velgAar(nr) {

            $.ajax({
                url: "?a=utvalg/vaktsjef/ukerapport_tabell/" + ukenr + '/' + nr,
                type: 'GET',
                success: function(html){
                    $('#kryss').html(html);
                },
                error: function(req,stat,err){
                    alert(err);
                }
            });
            aaret = nr;
        }

    </script>
    <div class="container">
        <h1>Utvalget &raquo; Vaktsjef &raquo; Ukesrapport</h1>

        <p>Velg År: <select onchange="velgAar(this.value)">
                <option value="<?php echo date('Y');?>"><?php echo date('Y');?></option>

                <?php
                $start = 2007;
                $årene = date('Y')-$start-1;
                //kryssing starta i 2007.
                for($i=date('Y')-1; $i>2006;$i--){

                    ?><option value="<?php echo $i;?>"><?php echo $i;?></option><?php

                }
                ?>
            </select></p>

        <p>Velg Uke: <select onchange="velgUke(this.value)">
                <option value="1">1</option>
                <?php
                $uker = 52;

                for($i=1; $i<=$uker;$i++){

                    ?><option value="<?php echo $i;?>"><?php echo $i;?></option><?php

                }
                ?>
            </select></p>


<div id="kryss">

    </div>
<?php
require_once(__DIR__ . '/../../static/bunn.php');
?>