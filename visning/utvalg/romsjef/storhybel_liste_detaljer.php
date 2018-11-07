<?php

require_once(__DIR__ . '/../topp_utvalg.php');

/* @var $lista \intern3\Storhybelliste */

?>
    <div class="container">
        <div class="col-lg-12">
            <h1>Utvalget &raquo; Romsjef &raquo; Storhybel administrasjon for
                &raquo; <?php echo $lista->getNavn(); ?></h1>

            [ <a href="?a=utvalg/romsjef/storhybel/liste">Liste</a> ] | [ <a href="?a=utvalg/romsjef/storhybel">Ny</a> ]
            <hr>

            <div class="alert alert-success fade in" id="success"
                 style="margin: auto; margin-top: 5%; display:none">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <p id="tilbakemelding-text"></p>
            </div>


            <div class="col-lg-6">

                <h2 id="status">Status: <?php echo $lista->getStatusTekst(); ?></h2>

                <?php if (!$lista->erFerdig()) { ?>
                    <p>
                        <?php if ($lista->erAktiv()) { ?>
                            <button class="btn btn-warning" onclick="deaktiver()">Deaktiver</button>
                        <?php } else { ?>
                            <button class="btn btn-warning" onclick="aktiver()">Aktivér</button>
                        <?php } ?>

                        <button class="btn btn-danger" onclick="slett()">Slett</button>

                        <button class="btn btn-info" onclick="neste()">Neste person</button>
                        <button class="btn btn-default" onclick="forrige()">Forrige person</button>
                        <button class="btn btn-primary" onclick="vis()">Lagre resultat</button>
                    </p>

                <?php } ?>

            </div>

            <?php if (!$lista->erFerdig()) { ?>
                <div class="col-lg-6">

                    <h3>Ledige rom</h3>

                    <p><select class="form-control" onchange="leggtilrom(this)">
                            <option>Velg</option>
                            <?php foreach ($alle_rom as $rom) {

                                if (in_array($rom, $ledige_rom)) {
                                    ?>

                                    <option value="<?php echo $rom->getId(); ?>"><?php echo $rom->getNavn(); ?>(LEDIG)
                                    </option>
                                <?php } else { ?>

                                    <option value="<?php echo $rom->getId(); ?>"><?php echo $rom->getNavn(); ?></option>
                                    <?php
                                }
                            }
                            ?>

                        </select></p>


                    <table class="table table-responsive">

                        <thead>
                        <tr>
                            <th>Romnummer</th>
                            <th>Romtype</th>
                            <th></th>
                        </tr>

                        </thead>

                        <tbody>
                        <?php foreach ($lista->getLedigeRom() as $rom) {
                            /* @var $rom \intern3\Rom */
                            ?>

                            <tr id="<?php echo $rom->getId(); ?>">
                                <td><?php echo $rom->getNavn(); ?></td>
                                <td><?php echo $rom->getType()->getNavn(); ?></td>
                                <td>
                                    <button class="btn btn-danger" onclick="fjernrom(<?php echo $rom->getId(); ?>)">
                                        Fjern
                                    </button>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>

            <?php } ?>

            <div class="col-lg-12">

                <div class="col-lg-6">
                    <h3>Rekkefølge</h3>
                    <p>(drag-and-drop for å endre, når lista er inaktiv.)</p>
                </div>


                <?php if (!$lista->erFerdig()) { ?>
                    <div class="col-lg-6">
                        <h3>Legg til beboere</h3>
                        <p>Disse blir lagt til nederst. Kan bare legge til de som ikke er på lista fra før.</p>

                        <p>
                            <select class="form-control" onchange="leggtilbeboer(this)">
                                <option>Velg</option>

                                <?php foreach ($beboerliste as $beboer) {
                                    /* @var $beboer \intern3\Beboer */ ?>

                                    <option value="<?php echo $beboer->getId(); ?>"><?php echo $beboer->getFulltNavn(); ?></option>

                                <?php } ?>
                            </select>

                        </p>


                    </div>

                <?php } ?>

                <?php if ($lista->erAktiv() || $lista->erFerdig()) { ?>
                <table class="table table-responsive table-hover grid">
                    <?php } else { ?>
                    <table class="table table-responsive table-hover grid" id="sort">
                        <?php } ?>

                        <thead>
                        <tr>
                            <th class="index">Nr.</th>
                            <th>Navn</th>
                            <th>Gammelt Rom</th>
                            <th>Nytt Rom</th>
                            <th>Ansiennitet</th>
                            <th>Klassetrinn</th>
                            <th></th>
                        </tr>
                        </thead>

                        <tbody>

                        <?php foreach ($lista->getRekkefolge() as $nummer => $beboer) {
                            /* @var $beboer \intern3\Beboer */
                            $klassen = '';
                            if ($nummer == $lista->getVelgerNr()) {
                                $klassen = 'danger';
                            }

                            ?>

                            <tr id="<?php echo $beboer->getId(); ?>" class="<?php echo $klassen; ?>">
                                <td class="index"><?php echo $nummer; ?></td>
                                <td><?php echo $beboer->getFulltNavn(); ?></td>
                                <td><?php echo $lista->getFordeling()[$beboer->getId()]->getGammeltRom()->getNavn(); ?></td>
                                <td><?php echo $lista->getFordeling()[$beboer->getId()]->getNyttRomId() !== null ? $lista->getFordeling()[$beboer->getId()]->getNyttRom()->getNavn() : ''; ?></td>
                                <td><?php echo $beboer->getAnsiennitet(); ?></td>
                                <td><?php echo $beboer->getKlassetrinn(); ?></td>
                                <td>
                                    <?php if (!$lista->erFerdig()) { ?>
                                        <button class="btn btn-danger"
                                                onclick="fjernbeboer(<?php echo $beboer->getId(); ?>)">Fjern
                                        </button>
                                    <?php } ?>
                                </td>
                            </tr>

                            <?php
                        } ?>
                        </tbody>


                    </table>


            </div>


        </div>
    </div>

    <div class="modal fade" id="commit-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Lagre</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="rom">
                    <p>
                        Når du trykker på knappen under, vil rommene fordeles til beboerene, og storhybellista
                        deaktiveres. Trykk kun på denne når lista er helt ferdig.
                        Det er ingen vei tilbake.
                    </p>

                    <p>
                        <button class="btn btn-danger" onclick="commit()">
                            LAGRE
                        </button>
                    </p>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Lukk</button>
                </div>
            </div>
        </div>
    </div>

    <script>

        function tilbakemelding(beskjed) {
            document.getElementById("success").style.display = "table";
            document.getElementById("tilbakemelding-text").innerHTML = beskjed;
        }

        var beboer_id;

        function updateDB(beboer_id, nummer) {

            $.ajax({
                type: 'POST',
                url: '?a=utvalg/romsjef/storhybel/liste/oppdater/<?php echo $lista->getId(); ?>',
                data: 'beboer_id=' + beboer_id + '&nummer=' + nummer,
                method: 'POST',
                success: function (data) {
                    tilbakemelding(data);
                },
                error: function (req, stat, err) {
                    alert(err);
                }
            });

        }

        function slett() {

            $.ajax({
                type: 'POST',
                url: '?a=utvalg/romsjef/storhybel/liste/slett/<?php echo $lista->getId(); ?>',
                data: '',
                method: 'POST',
                success: function (data) {
                    window.location = '?a=utvalg/romsjef/storhybel/liste';
                },
                error: function (req, stat, err) {
                    alert(err);
                }
            });

        }

        function aktiver() {

            $.ajax({
                type: 'POST',
                url: '?a=utvalg/romsjef/storhybel/liste/aktiver/<?php echo $lista->getId(); ?>',
                data: '',
                method: 'POST',
                success: function (data) {
                    tilbakemelding(data);
                    $("#status").text("Status: Aktiv");
                },
                error: function (req, stat, err) {
                    alert(err);
                }
            });

        }

        function deaktiver() {

            $.ajax({
                type: 'POST',
                url: '?a=utvalg/romsjef/storhybel/liste/deaktiver/<?php echo $lista->getId(); ?>',
                data: '',
                method: 'POST',
                success: function (data) {
                    tilbakemelding(data);
                    $("#status").text("Status: Inaktiv");
                    //window.location.reload();
                },
                error: function (req, stat, err) {
                    alert(err);
                }
            });

        }

        function fjernbeboer(beboer_id) {

            $.ajax({
                type: 'POST',
                url: '?a=utvalg/romsjef/storhybel/liste/fjernbeboer/<?php echo $lista->getId(); ?>',
                data: 'beboer_id=' + beboer_id,
                method: 'POST',
                success: function (data) {
                    $("#" + beboer_id).hide();
                    tilbakemelding(data);
                },
                error: function (req, stat, err) {
                    alert(err);
                }
            });

        }

        function fjernrom(rom_id) {

            $.ajax({
                type: 'POST',
                url: '?a=utvalg/romsjef/storhybel/liste/fjernrom/<?php echo $lista->getId(); ?>',
                data: 'rom_id=' + rom_id,
                method: 'POST',
                success: function (data) {
                    $("#" + rom_id).hide();
                    tilbakemelding(data);
                },
                error: function (req, stat, err) {
                    alert(err);
                }
            });

        }

        function leggtilrom(selected) {
            $.ajax({
                type: 'POST',
                url: '?a=utvalg/romsjef/storhybel/liste/leggtilrom/<?php echo $lista->getId(); ?>',
                data: 'rom_id=' + selected.value,
                method: 'POST',
                success: function (data) {
                    tilbakemelding(data);
                    selected.selectedIndex = 0;

                },
                error: function (req, stat, err) {
                    alert(err);
                }
            });

        }

        function neste() {
            $.ajax({
                type: 'POST',
                url: '?a=utvalg/romsjef/storhybel/liste/neste/<?php echo $lista->getId(); ?>',
                data: '',
                method: 'POST',
                success: function (data) {
                    tilbakemelding(data);
                },
                error: function (req, stat, err) {
                    alert(err);
                }
            });

        }

        function forrige() {
            $.ajax({
                type: 'POST',
                url: '?a=utvalg/romsjef/storhybel/liste/forrige/<?php echo $lista->getId(); ?>',
                data: '',
                method: 'POST',
                success: function (data) {
                    tilbakemelding(data);
                },
                error: function (req, stat, err) {
                    alert(err);
                }
            });
        }

        function leggtilbeboer(selected) {
            $.ajax({
                type: 'POST',
                url: '?a=utvalg/romsjef/storhybel/liste/leggtilbeboer/<?php echo $lista->getId(); ?>',
                data: 'beboer_id=' + selected.value,
                method: 'POST',
                success: function (data) {
                    tilbakemelding(data);
                    selected.selectedIndex = 0;

                },
                error: function (req, stat, err) {
                    alert(err);
                }
            });
        }

        var fixHelperModified = function (e, tr) {
                var $originals = tr.children();
                var $helper = tr.clone();
                $helper.children().each(function (index) {
                    $(this).width($originals.eq(index).width())
                });

                beboer_id = tr[0].id;

                return $helper;
            },
            updateIndex = function (e, ui) {
                $('td.index', ui.item.parent()).each(function (i) {
                    $(this).html(i + 1);
                });

                var nummer = $('#' + beboer_id).children()[0].innerHTML;
                updateDB(beboer_id, nummer);

            };

        $("#sort tbody").sortable({
            helper: fixHelperModified,
            stop: updateIndex
        }).disableSelection();

        function vis() {
            $("#commit-modal").modal("show");
        }

        function commit() {
            $.ajax({
                type: 'POST',
                url: '?a=utvalg/romsjef/storhybel/liste/commit/<?php echo $lista->getId(); ?>',
                data: '',
                method: 'POST',
                success: function (data) {
                    tilbakemelding(data);
                    $("#commit-modal").modal("hide");
                },
                error: function (req, stat, err) {
                    alert(err);
                }
            });
        }

    </script>

<?php

require_once(__DIR__ . '/../../static/bunn.php');

