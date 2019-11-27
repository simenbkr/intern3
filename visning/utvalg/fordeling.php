<?php

require_once "topp_utvalg.php";
?>
    <div class="container">

        <h1>Utvalg » Fordeling</h1>

        <div class="col-lg-6">

            <p>
                Her kan utvalget teste/eksperimentere med ulike parametere for å finne optimale fordelinger av
                roller. Stjernemerka felt er obligatoriske, de andre har default-verdier gitt i grått, dersom intet
                input gis. NB: Skriv inn datoer på gitt format (ISO8601).
            </p>
            <form id="f" method="post" action="">
                <table class="table table-responsive">
                    <tr>
                        <td>Start (*) (YYYY-MM-DD)</td>
                        <td><input name="start" placeholder="<?php echo date('Y') . "-08-20"; ?>" class="form-control"/>
                        </td>
                    </tr>

                    <tr>
                        <td>Slutt (*) (YYYY-MM-DD)</td>
                        <td><input name="slutt" placeholder="<?php echo date('Y') . "-12-21"; ?>" class="form-control"/>
                        </td>
                    </tr>

                    <tr>
                        <td>Antall fridager for Torild/Ansatt</td>
                        <td><input name="fri" value="0" type="number" class="form-control"/></td>
                    </tr>

                    <tr>
                        <td>Antall fridager</td>
                        <td><input name="fridager" value="0" type="number" class="form-control"/></td>
                    </tr>

                    <tr>
                        <td>Minimum antall på full vakt</td>
                        <td><input name="min_full" class="form-control" placeholder="10" type="number"/></td>
                    </tr>

                    <tr>
                        <td>Maksimum antall på full vakt</td>
                        <td><input name="max_full" class="form-control" placeholder="40" type="number"/></td>
                    </tr>

                    <tr>
                        <td>Minimum antall på full regi</td>
                        <td><input name="min_regi" class="form-control" placeholder="10" type="number"/></td>
                    </tr>

                    <tr>
                        <td>Maksimum antall på full regi</td>
                        <td><input name="max_regi" class="form-control" placeholder="40" type="number"/></td>
                    </tr>

                    <tr>
                        <td>Minimum antall halv vakt/regi</td>
                        <td><input name="min_gauder" class="form-control" placeholder="10" type="number"/></td>
                    </tr>

                </table>
                <input id="b" type="submit" class="btn btn-primary" value="Send">
            </form>

        </div>

        <div class="col-lg-6">
            <p id="infotekst"></p>

            <table id="data" class="table table-responsive table-condensed"></table>
        </div>
    </div>


    <script>

        $(document).ready(function () {

            var $form = $('form');
            $form.submit(function () {
                $.post($(this).attr('action'), $(this).serialize(), function (response) {
                    document.getElementById('infotekst').innerText = response[0];
                    buildTable(response);
                }, 'json');
                return false;
            })
        });

        function buildTable(data) {
            emptyTable();
            var cols = ['Halv vakt/regi', 'Full vakt', 'Full regi', 'Vakter', 'Regitimer'];

            var table = document.getElementById('data');
            var tr = table.insertRow(-1);

            for (var i = 0; i < cols.length; i++) {
                var th = document.createElement('th');
                th.innerHTML = cols[i];
                tr.appendChild(th);
            }

            for (var k in data) {

                if (isNaN(data[k][0])) {
                    continue;
                }

                tr = table.insertRow(-1);
                for (var j = 0; j < 5; j++) {
                    var cell = tr.insertCell(-1);
                    cell.innerHTML = data[k][j];
                }
            }
        }

        function emptyTable() {
            $("#data tr").remove();
        }

    </script>


<?php
require_once(__DIR__ . '/../static/bunn.php');