<?php

require_once(__DIR__ . '/../topp_utvalg.php');
?>
    <div class="container">
    <div class="col-lg-12">
        <h1>Utvalget &raquo; Romsjef &raquo; Ny Storhybelliste</h1>

        <p>
            [ <a href="?a=utvalg/romsjef/storhybel/liste">Liste</a> ] | [ Ny
                Storhybelliste ] [ <a href="?a=utvalg/romsjef/storhybel/korr">Ny Korrhybelliste</a> ]
            [ <a href="?a=utvalg/romsjef/storhybel/storparhybel">Ny Parhybelliste</a> ]
        </p>
        <hr>

        <?php require_once(__DIR__ . '/../../static/tilbakemelding.php'); ?>

        <p>Her kan du starte en ny storhybelliste.</p>
        <p>Husk å sjekke at ansienniteten er oppdatert og korrekt,
            og at alle beboere som (eventuelt) skal flytte ut har blitt flytta ut.</p>

        <p>Du kan endre på dette før storhybellista aktiveres.</p>

        <div class="panel-group" id="accordion">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapse1">
                            Beboerliste med ansiennitet</a>
                    </h4>
                </div>
                <div id="collapse1" class="panel-collapse collapse">
                    <div class="panel-body">

                        <a href="?a=utvalg/romsjef/ansiennitet" class="btn btn-warning">Rediger ansiennitet her</a>

                        <table class="table table-responsive">
                            <thead>
                            <tr>
                                <th>Navn</th>
                                <th>Ansiennitet</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($beboerliste as $beboer) {
                                /* @var $beboer \intern3\Beboer */
                                ?>

                                <tr>
                                    <td><?php echo $beboer->getFulltNavn(); ?></td>
                                    <td><?php echo $beboer->getAnsiennitet(); ?></td>
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
                            Ledige rom</a>
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

        <form action="?a=utvalg/romsjef/storhybel/ny" method="post">

            <button class="btn btn-danger">Start ny storhybelliste</button>

        </form>
    </div>


<?php

require_once(__DIR__ . '/../../static/bunn.php');