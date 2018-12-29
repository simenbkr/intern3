<?php

require_once(__DIR__ . '/../static/topp.php');

/* @var $lista \intern3\Storhybelliste */
/* @var $min_tur bool */
/* @var $persnummer int */
/* @var $beboers_rom \intern3\Rom */
/* @var $aktiv_velger \intern3\StorhybelVelger */


?>
    <style>
        .table-inactive td {
            background-color: #C0C0C0;
            color: #232525;
        }
    </style>

    <script>

        function vis(id) {
            $("#rom").load("?a=storhybel/<?php echo $lista->getId(); ?>/modal/" + id);
            $("#velg-modal").modal("show");
        }

        <?php if($kan_passe) { ?>
        function pass() {
            $.ajax({
                type: 'POST',
                url: '?a=storhybel/<?php echo $lista->getId(); ?>/pass',
                data: 'sid=' + '<?php echo $lista->getId(); ?>',
                method: 'POST',
                success: function (data) {
                    window.location.reload();
                },
                error: function (req, stat, err) {
                    alert(err);
                }
            });
        }

        <?php } ?>


    </script>

    <div class="col-lg-12">
        <h1>Storhybelliste</h1>

        <hr>

        <div class="alert alert-success fade in" id="success"
             style="margin: auto; margin-top: 5%; display:none">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <p id="tilbakemelding-text"></p>
        </div>

        <?php require_once (__DIR__ . '/../static/tilbakemelding.php'); ?>

        <?php if ($min_tur) { ?>
            <div class="col-lg-6">

                <h3><b>Det er din tur!</b></h3>
                <p>Du har 24t på å velge rom. Kontakt nestemann når du er ferdig. Nestemann
                    er <?php echo $lista->getNeste()->getNavn(); ?>.</p>


                <?php if ($kan_passe) { ?>
                    <p>
                        Du kan passe, dersom du ønsker å benytte din neste anledning til å velge rom.
                    </p>
                    <p>
                        <b>Merk: det er ingen vei tilbake etter at du trykker på knappen under.</b>
                    </p>
                    <p>
                        <button class="btn btn-danger" onclick="pass()">PASS</button>
                    </p>
                <?php } ?>
                <table class="table table-responsive table-condensed">
                    <thead>
                    <tr>
                        <th>Romnummer</th>
                        <th>Romtype</th>
                        <th></th>
                    </tr>
                    </thead>

                    <tbody>
                    <?php foreach ($aktiv_velger->getBeboere() as $beboer) {
                        /* @var \intern3\Beboer $beboer */
                        ?>

                        <tr>
                            <td><?php echo $beboer->getRom()->getNavn(); ?></td>
                            <td><?php echo $beboer->getRom()->getType()->getNavn(); ?></td>
                            <td>
                                <button class="btn btn-warning"
                                        onclick="vis(<?php echo $beboer->getRom()->getId(); ?>)">
                                    Velg
                                </button>
                            </td>

                        </tr>
                    <?php }
                      ?>


                    <?php foreach ($lista->getLedigeRom() as $rom) {
                        /* @var $rom \intern3\Rom */ ?>

                        <tr>
                            <td><?php echo $rom->getNavn(); ?></td>
                            <td><?php echo $rom->getType()->getNavn(); ?></td>
                            <td>
                                <button type="button" class="btn btn-primary"
                                        onclick="vis(<?php echo $rom->getId(); ?>)">
                                    Velg
                                </button>
                            </td>
                        </tr>


                    <?php } ?>
                    </tbody>

                </table>

            </div>
        <?php } else { ?>

            Du er nummer <?php echo $persnummer; ?> på lista, og vi er nå på nummer <?php echo $lista->getVelgerNr(); ?>.

            <div class="col-lg-3">

                <h3>Ledige rom</h3>

                <table class="table table-responsive">

                    <thead>
                    <tr>
                        <th>Romnummer</th>
                        <th>Romtype</th>
                    </tr>

                    </thead>

                    <tbody>
                    <?php foreach ($lista->getLedigeRom() as $rom) {
                        /* @var $rom \intern3\Rom */
                        ?>

                        <tr id="<?php echo $rom->getId(); ?>">
                            <td><?php echo $rom->getNavn(); ?></td>
                            <td><?php echo $rom->getType()->getNavn(); ?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>

        <?php } ?>


        <div class="col-lg-3">
            <h3>Kart</h3>
            (Klikk for å se full størrelse.)
            <a href="beboerkart/kart.jpg"><img class="img-thumbnail" src="beboerkart/kart.jpg"></a>
        </div>

        <div class="col-lg-6">
            <table class="table table-responsive table-hover grid table-condensed">
                <thead>
                <tr>
                    <th class="index">Nr.</th>
                    <th>Navn</th>
                    <th>Ansiennitet</th>
                    <th>Klassetrinn</th>
                    <th>Rom forrige semester</th>
                    <th>Rom neste semester</th>
                </tr>
                </thead>

                <tbody>

                <?php foreach ($lista->getRekkefolge() as $velger) {
                    /* @var $velger \intern3\StorhybelVelger */
                    $nummer = $velger->getNummer();
                    $klassen = '';
                    if ($nummer == $lista->getVelgerNr()) {
                        $klassen = 'danger';
                    }

                    if ($nummer == $persnummer) {
                        $klassen = 'success';
                    }

                    if ($nummer == $lista->getVelgerNr() && $nummer == $persnummer) {
                        $klassen = 'warning';
                    }

                    if ($nummer < $lista->getVelgerNr()) {
                        $klassen = 'table-inactive';
                    }

                    ?>
                    <tr id="<?php echo $velger->getVelgerId(); ?>" class="<?php echo $klassen; ?>">
                        <td class="index"><?php echo $nummer; ?></td>
                        <td><?php echo $velger->getNavn(); ?></td>
                        <td><?php echo $velger->getAnsiennitet(); ?></td>
                        <td><?php echo $velger->getKlassetrinn(); ?></td>
                        <td><?php echo $lista->getFordeling()[$velger->getVelgerId()]->getGammleRomAsString(); ?></td>
                        <td><?php echo $lista->getFordeling()[$velger->getVelgerId()]->getNyttRomId() !== null ? $lista->getFordeling()[$velger->getVelgerId()]->getNyttRom()->getNavn() : ''; ?></td>
                    </tr>

                    <?php
                } ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="velg-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Velg rom</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="rom">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Lukk</button>
                </div>
            </div>
        </div>
    </div>

<?php

require_once(__DIR__ . '/../static/bunn.php');

