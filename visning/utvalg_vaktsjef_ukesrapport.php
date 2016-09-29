<?php
require_once('topp_utvalg.php');
//TODO: Lage drop-down meny sånn at man kan generere rapport for vilkårlig uke.
?>
    <script>
        var ukenr = null;
        var aaret = 2016;
        function velgUke(nr) {
            $.get('?a=utvalg/vaktsjef/ukerapport_tabell/' + nr + '/' + aaret, function (data) {
                $('#kryss').html(data);
                ukenr = nr;
            });
        }

        function velgAar(nr) {
            $.get('?a=utvalg/vaktsjef/ukerapport_tabell/'+ ukenr +'/' + nr, function (data) {
                $('#kryss').html(data);
                aaret = nr;
            });
        }

    </script>
    <div class="container">
        <h1>Utvalget &raquo; Vaktsjef &raquo; Ukesrapport</h1>
        <p>Velg Uke: <select onchange="velgUke(this.value)">
                <option value="0">- Velg Uke -</option>

                <?php
                $uker = 52;

                for($i=1; $i<=$uker;$i++){

                    ?><option value="<?php echo $i;?>"><?php echo $i;?></option><?php

                }
                ?>
            </select></p>


        <p>Velg År: <select onchange="velgAar(this.value)">
                <option value="2016">2016</option>

                <?php
                $start = 2007;
                $årene = date('Y')-$start;
                $uker = 52;

                for($i=$start; $i<=date('Y');$i++){

                    ?><option value="<?php echo $i;?>"><?php echo $i;?></option><?php

                }
                ?>
            </select></p>





<div id="kryss">

    </div>
<?php
include('bunn.php');
?>