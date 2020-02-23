<?php
require_once(__DIR__ . '/../static/topp.php');

?>
    <link rel="stylesheet" type="text/css" href="css/dataTables.css"/>
    <script type="text/javascript" src="js/dataTables.js"></script>

    <script>
        function leggTil() {
            var elem = document.getElementById('add');
            var epost = elem.value;
            $.ajax({
                url: '?a=jubileum/single',
                type: 'POST',
                data: 'epost=' + epost,
                success: function (res) {
                    tilbakemelding(res);
                    elem.value = '';
                    var table = document.getElementById('tabellen');
                    var row = table.insertRow(1);
                    var cell1 = row.insertCell(0);
                    var cell2 = row.insertCell(1);
                    cell1.innerHTML = epost;
                    cell2.innerHTML = '<button class="btn btn-danger" onclick="del(\'' + epost + '\')">Slett</button>'
                    row.setAttribute('id', epost);
                }
            })
        }

        function del(email) {
            $.ajax({
                url: '?a=jubileum/' + email,
                type: 'DELETE',
                success: function (result) {
                    document.getElementById(email).remove();
                    tilbakemelding(result);
                }
            });
        }

        function tilbakemelding(beskjed) {
            document.getElementById("success").style.display = "table";
            document.getElementById("tilbakemelding-text").innerHTML = beskjed;
        }

        $(document).ready(function () {
            var table = $('#tabellen').DataTable({
                "paging": true,
                "searching": true,
                "scrollY": "60vh",
                "scrollCollapse": true,
            });
        });


    </script>


    <div class="container">

        <h1>Jubileum &raquo; Jubileumets E-postliste</h1>

        <p>Disse kan også administreres fra Husfars og Sekretærs Google konto på <a
                    href="https://admin.google.com">admin.google.com</a>
            eller <a href="https://groups.google.com">groups.google.com</a>.</p>

        <p>Knapper kan respondere tregt ettersom det må kommunisere med Google sitt API. Trykk derfor kun en gang, og
            vent
            til tilbakemelding dukker opp.</p>

        <p>
            Hvis du vil laste opp mange e-poster samtidig, bruk filopplasting. <a href="?a=jubileum/eksempel">Se
                eksempel på format her</a>.
        </p>

        <form action="?a=jubileum/multiple" method="POST" enctype="multipart/form-data">
            Velg tekstfil
            <input class='form-control' type="file" name="tekstfil" id="tekstfil">
            <input class="btn btn-primary" type="submit" value="Send" name="send">
        </form>


        <hr>

        <?php require_once __DIR__ . '/../static/tilbakemelding.php'; ?>

        <div class="alert alert-success fade in" id="success"
             style="margin: auto; margin-top: 5%; display:none">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <p id="tilbakemelding-text"></p>
        </div>
        <p></p>


        <div class="input-group">
            <input type="email" id="add" class="form-control">
            <span class="input-group-btn">
                <button class="btn btn-default" type="button" onclick="leggTil()">Legg til</button>
            </span>
        </div>

        <hr>


        <table class="table table-bordered table-responsive table-condensed" id="tabellen">
            <thead>
            <tr>
                <th>Epost</th>
                <th>Meld av</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($deltakere as $deltaker) { ?>
                <tr id="<?php echo $deltaker['email']; ?>">
                    <td><?php echo $deltaker['email']; ?></td>
                    <td>
                        <button class="btn btn-danger" onclick="del('<?php echo $deltaker['email']; ?>')">Slett</button>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>

    </div>
<?php
require_once(__DIR__ . '/../static/bunn.php');