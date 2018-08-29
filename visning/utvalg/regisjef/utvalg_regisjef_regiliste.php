<?php
require_once(__DIR__ . '/../topp_utvalg.php');
?>

    <div class="col-lg-12">


        <h1>Utvalget » Regisjef » Regiliste</h1>
        <hr>


        <?php require_once (__DIR__ . '/../../static/tilbakemelding.php'); ?>

        <script>


            function slett(id){
                $.ajax({
                    type: 'POST',
                    url: '?a=utvalg/regisjef/regiliste/slett/' + id,
                    data: '',
                    method: 'POST',
                    success: function (data) {
                        //window.location.replace("?a=utvalg/regisjef/regiliste/");
                        location.reload();
                    },
                    error: function (req, stat, err) {
                        alert("Noe gikk galt!");
                    }
                })
            }

            function defaultRegi(){
                $.ajax({
                    type: 'POST',
                    url: '?a=utvalg/regisjef/regiliste/regi/',
                    data: '',
                    method: 'POST',
                    success: function (data) {
                        //window.location.replace("?a=utvalg/regisjef/regiliste/");
                        location.reload();
                    },
                    error: function (req, stat, err) {
                        alert("Noe gikk galt!");
                    }
                })
            }

        </script>

        <p>
            <a href="?a=utvalg/regisjef/regiliste/opprett">
                <button class="btn btn-primary">Opprett ny regiliste</button>
            </a>

            <button class="btn btn-info" onclick="defaultRegi()">Generer regi-liste (tar med alle med regi uten utvalgsverv).</button>
        </p>


        <div class="col-md-12">
            <h2>Regilister</h2>
            <hr>
            <div class="alert alert-success fade in" id="success" style="margin: auto; margin-top: 5%; display:none">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <p id="tilbakemelding-text"></p>
            </div>
            <p></p>



            <table class="table table-bordered table-responsive" id="tabellen">
                <thead>
                <tr>
                    <th>Navn</th>
                    <th>Antall</th>
                    <th>Slett</th>
                </tr>
                </thead>
                <tbody>
                <?php

                foreach ($regilister as $regiliste) {
                    /* @var \intern3\Regiliste $regiliste */
                    ?>
                    <tr>
                        <td><a href="?a=utvalg/regisjef/regiliste/<?php echo $regiliste->getId(); ?>"><?php echo $regiliste->getNavn(); ?></a></td>
                        <td><?php echo count($regiliste->getBeboerliste()); ?></td>
                        <td><button class="btn btn-danger" onclick="slett(<?php echo $regiliste->getId(); ?>)">Slett</button></td>
                    </tr>
                    <?php
                }

                ?>
                </tbody>
            </table>

        </div>


    </div>
<?php
require_once(__DIR__ . '/../../static/bunn.php');
