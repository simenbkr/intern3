<?php

require_once(__DIR__ . '/../topp_utvalg.php');
?>
    <script>

        var ids = [];

        function addRow() {
            if (ids.length === 0) {
                nyId = 1;
            } else {
                var nyId = parseInt(ids[ids.length - 1]) + 1;
            }
            var html = "<tr><th>Par " + nyId + "</th><td><div id='" + nyId + "'></div></td><td><button class='btn btn-danger' type='button' onclick='removeRow($(this).parent())'>-</button></td></tr>";

            $("#tabellen").append(html);
            $("#" + nyId).load('?a=utvalg/romsjef/storhybel/storparhybel_select');
            ids.push(nyId + "");
        }

        function sendInn() {

            var parliste = [];
            for (var i = 0; i < ids.length; i++) {
                parliste.push($("#" + ids[i] + " option:selected").map(function () {
                    return this.value
                }).get());
            }

            $.ajax({
                type: 'POST',
                url: '?a=utvalg/romsjef/storhybel/sp/',
                data: 'parliste=' + JSON.stringify(parliste),
                method: 'POST',
                success: function (data) {
                    window.location = '?a=utvalg/romsjef/storhybel/liste/' + data;
                },
                error: function (req, stat, err) {
                    alert(err);
                }
            });
        }

        function removeRow(obj) {
            var row = obj.parent();

            var id = row.children("td").children("div").attr('id');
            var index = ids.indexOf(id);

            if (index > -1) {
                ids.splice(index, 1);
            }
            row.remove();
        }


        $(document).ready(function () {
            addRow();
        });

        function onchangeFunc(){

            console.log("asdasdtasyt")

        }

    </script>

    <div class="container">
        <div class="col-lg-12">
            <h1>Utvalget &raquo; Romsjef &raquo; Ny Parhybelliste</h1>

            <p>
                [ <a href="?a=utvalg/romsjef/storhybel/liste">Liste</a> ] | [ <a href="?a=utvalg/romsjef/storhybel">Ny
                    Storhybelliste</a> ] [ <a href="?a=utvalg/romsjef/storhybel/korr">Ny Korrhybelliste</a> ]
                [ Ny Parhybelliste ]
            </p>
            <hr>

            <?php require_once(__DIR__ . '/../../static/tilbakemelding.php'); ?>


            <p>Velg alle par som skal stå på lista (protip: Legg til alle bokser før du velger noen).
            </p>
            <p>
                Parene blir automagisk sortert etter ansiennitet og klassetrinn (her teller maks klassetrinn).
                Du kan endre rekkefølgen, legge til og fjerne rom etter opprettelse.
            </p>

            <div class="col-lg-6">
                <h3>Velg par</h3>
                <form method="post" action="">

                    <table class="table table-responsive" id="tabellen">

                    </table>

                </form>
                <button class="btn btn-primary" onclick="sendInn()">Opprett liste</button>
                <button class="btn btn-info pull-right" onclick="addRow()">+</button>
            </div>

            <div class="col-lg-6">

                <h3>Oppsatte rom</h3>

                <table class="table table-responsive">
                    <thead>
                        <tr>
                            <th>Rom</th>
                            <th>Type</th>
                        </tr>
                    </thead>

                <?php foreach($ledige_rom as $rom) {
                    /* @var \intern3\Rom $rom */
                    ?>
                    <tr>
                        <td><?php echo $rom->getNavn(); ?></td>
                        <td><?php echo $rom->getType()->getNavn(); ?></td>
                    </tr>
                <?php
                }
                ?>
                </table>
            </div>

        </div>
    </div>

<?php

require_once(__DIR__ . '/../../static/bunn.php');
