<?php

require_once(__DIR__ . '/../topp_utvalg.php');
?>
    <style>
        .table-responsiver {
            max-height: 350px;
            overflow-y: scroll;
        }
    </style>

    <script>

        var beboerlista = [];

        function add(obj) {

            beboerlista.push(obj.value);
            obj.onclick = function () {
                deselect(this)
            }
            document.getElementById('antall').innerText = "Det er forløpig " + beboerlista.length + " på lista.";
        }

        function deselect(obj) {
            var index = beboerlista.indexOf(obj.value);

            if (index > -1) {
                beboerlista.splice(index, 1);
            }
            obj.onclick = function () {
                add(this);
            }
            document.getElementById('antall').innerText = "Det er forløpig " + beboerlista.length + " på lista.";
        }

        function submit() {
            $.ajax({
                type: 'POST',
                url: '?a=utvalg/romsjef/storhybel/korr',
                data: 'beboer_ids=' + JSON.stringify(beboerlista),
                method: 'POST',
                success: function (data) {
                    window.location = '?a=utvalg/romsjef/storhybel/liste/' + data;
                },
                error: function (req, stat, err) {
                    alert(err);
                }
            });
        }

    </script>

    <div class="container">
        <div class="col-lg-12">
            <h1>Utvalget &raquo; Romsjef &raquo; Ny Korrhybelliste</h1>

            <p>
                [ <a href="?a=utvalg/romsjef/storhybel/liste">Liste</a> ] | [ <a href="?a=utvalg/romsjef/storhybel/arkiv">Arkiv</a>
                ] | [ <a href="?a=utvalg/romsjef/storhybel"> Ny
                    Storhybelliste</a> ] [ Ny Korrhybelliste ]
                [ <a href="?a=utvalg/romsjef/storhybel/storparhybel">Ny Parhybelliste</a> ]
            </p>
            <hr>

            <div class="panel-group" id="accordion">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapse1">
                                Velg fra Beboerliste</a>
                        </h4>
                    </div>
                    <div id="collapse1" class="panel-collapse collapse table-responsiver">
                        <div class="panel-body">

                            <table class="table table-responsive table-condensed">
                                <thead>
                                <tr>
                                    <th>Navn</th>
                                    <th>Ansiennitet</th>
                                    <th>Velg</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($beboerliste as $beboer) {
                                    /* @var $beboer \intern3\Beboer */
                                    ?>

                                    <tr>
                                        <td><?php echo $beboer->getFulltNavn(); ?></td>
                                        <td><?php echo $beboer->getAnsiennitet(); ?></td>
                                        <td><input type="checkbox" onclick="add(this)"
                                                   value="<?php echo $beboer->getId(); ?>"/></td>
                                    </tr>

                                    <?php
                                }
                                ?>
                                </tbody>


                            </table>


                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapse2">
                                Se over Ledige rom</a>
                        </h4>
                    </div>
                    <div id="collapse2" class="panel-collapse collapse">
                        <div class="panel-body">

                            <table class="table table-responsive">
                                <thead>
                                <tr>
                                    <th>Romnummer</th>
                                    <th>Romtype</th>
                                </tr>
                                </thead>

                                <tbody>

                                <?php foreach ($ledige_rom as $rom) {
                                    /* @var $rom \intern3\Rom */
                                    ?>

                                    <tr>
                                        <td><?php echo $rom->getNavn(); ?></td>
                                        <td><?php echo $rom->getType()->getNavn(); ?></td>
                                    </tr>

                                    <?php

                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>


            <h3 id="antall">Det er forløpig 0 på lista.</h3>
            <button class="btn btn-danger" onclick="submit()">Start ny korrhybelliste</button>

        </div>

    </div>

<?php

require_once(__DIR__ . '/../../static/bunn.php');
