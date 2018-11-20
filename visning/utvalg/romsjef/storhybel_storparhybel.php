<?php

require_once(__DIR__ . '/../topp_utvalg.php');
?>
    <link rel="stylesheet" href="css/chosen.min.css">
    <script src="js/chosen.jquery.min.js"></script>

    <script>

        var ids = ['1'];

        function addRow() {

            var nyId = parseInt(ids[ids.length - 1]) + 1;
            var html = "<tr><th>Velg</th><td><div id='" + nyId + "'></div></td></tr>";

            $("#tabellen").append(html);
            $("#" + nyId).load('?a=utvalg/romsjef/storhybel/storparhybel_select');
            ids.push(nyId + "");
        }

        function sendInn() {

            //e1.preventDefault();

            var parliste = [];
            for(var i = 0; i < ids.length; i++) {
                parliste.push($("#" + ids[i] + " option:selected").map(function(){ return this.value }).get());
            }

            $.ajax({
                type: 'POST',
                url: '?a=utvalg/romsjef/storhybel/sp/',
                data: 'parliste=' + JSON.stringify(parliste),
                method: 'POST',
                success: function (data) {
                    console.log(data);
                },
                error: function (req, stat, err) {
                    alert(err);
                }
            });

        }


        $(document).ready(function () {
            $("#1").load('?a=utvalg/romsjef/storhybel/storparhybel_select');
        });
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


            <form method="post" action="">

                <table class="table table-responsive" id="tabellen">

                    <tr>
                        <th>Velg</th>
                        <td><div id="1"></div></td>
                    </tr>

                </table>

            </form>
            <button onclick="sendInn()">Klikk</button>
            <button onclick="addRow()">test</button>

        </div>
    </div>

<?php

require_once(__DIR__ . '/../../static/bunn.php');
