<?php

require_once(__DIR__ . '/../../topp_utvalg.php');

?>

    <script>

        var year = '<?php echo date('Y'); ?>'

        function velgYear(y) {
            year = y;
            $.ajax({
                url: "?a=utvalg/romsjef/soknad/year/" + y,
                type: 'GET',
                success: function (html) {
                    $("#tabell").html(html);
                },
                error: function (req, stat, err) {
                    alert(err);
                }
            });
        }

        window.onload = function () {
            velgYear(year);

            document.getElementById("knapp").onclick = function() {
                location.href = "?a=utvalg/romsjef/soknad/eksporter/" + year;
            }

            document.getElementById("knapp2").onclick = function() {
                location.href = "?a=utvalg/romsjef/soknad/clean";
            }

        }

    </script>


    <div class="container">

        <div class="col-lg-12">
            <h1>Utvalget » Romsjef » Søknader</h1>

            Velg år: <select onchange="velgYear(this.value)" class="form-control">
                <option value="<?php echo date('Y'); ?>">Søknadsår: <?php echo date('Y'); ?></option>

                <?php
                $start = 2019;
                $årene = date('Y') - $start - 1;
                for ($i = date('Y') - 1; $i > 2018; $i--) {

                    ?>
                    <option value="<?php echo $i; ?>"><?php echo "Søknadsår: $i"; ?></option><?php

                }
                ?>
            </select>

            <p><button id="knapp2" class="btn btn-danger pull-right">Slett duplikater/tomme.</button></p>
            <p><button id="knapp" class="btn btn-warning pull-right">Eksporter data</button></p>

            <hr>

            <div id="tabell">
            </div>


        </div>
    </div>
<?php


require_once(__DIR__ . '/../../../static/bunn.php');

