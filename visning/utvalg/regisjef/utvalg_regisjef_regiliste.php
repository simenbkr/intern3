<?php
require_once(__DIR__ . '/../topp_utvalg.php');
?>

    <div class="col-lg-12">


        <h1>Utvalget » Regisjef » Regiliste</h1>
        <hr>


        <?php require_once (__DIR__ . '/../../static/tilbakemelding.php'); ?>

        <script>


        </script>

        <p>
            <a href="?a=utvalg/regisjef/regiliste/opprett">
                <button class="btn btn-primary">Opprett ny regiliste</button>
            </a>
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
