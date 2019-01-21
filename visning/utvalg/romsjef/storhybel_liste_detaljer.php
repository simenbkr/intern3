<?php

namespace intern3;
require_once(__DIR__ . '/../topp_utvalg.php');


/* @var $lista \intern3\Storhybelliste */
function rutime($ru, $rus, $index)
{
    return ($ru["ru_$index.tv_sec"] * 1000 + intval($ru["ru_$index.tv_usec"] / 1000))
        - ($rus["ru_$index.tv_sec"] * 1000 + intval($rus["ru_$index.tv_usec"] / 1000));
}

$rustart = getrusage();

?>
    <div class="container">
        <div class="col-lg-12">
            <h1>Utvalget &raquo; Romsjef &raquo; Administrasjon for
                &raquo; <?php echo $lista->getNavn(); ?></h1>

            [ <a href="?a=utvalg/romsjef/storhybel/liste">Liste</a> ] | [ <a href="?a=utvalg/romsjef/storhybel/arkiv">Arkiv</a>
            ] |
            [ <a href="?a=utvalg/romsjef/storhybel"> Ny
                Storhybelliste</a> ] [ <a href="?a=utvalg/romsjef/storhybel/korr">Ny Korrhybelliste</a> ]
            [ <a href="?a=utvalg/romsjef/storhybel/storparhybel">Ny Parhybelliste</a> ]
            <hr>

            <div class="alert alert-success fade in" id="success"
                 style="margin: auto; margin-top: 5%; display:none">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <p id="tilbakemelding-text"></p>
            </div>

            <?php require_once(__DIR__ . '/../../static/tilbakemelding.php'); ?>


            <div class="col-lg-6">

                <h2 id="status">Status: <?php echo $lista->getStatusTekst(); ?></h2>

                <?php if (!$lista->erArkivert()) { ?>
                    <p>
                        <button class="btn btn-danger" onclick="arkiver()">Arkiver</button>
                    </p>
                    <?php
                }
                if (!$lista->erFerdig() && $lista->getStatusTekst() !== 'Arkivert') { ?>
                    <p>
                        <?php if ($lista->erAktiv()) { ?>
                            <button class="btn btn-warning" onclick="deaktiver()" id="aktiveringsknapp">Deaktiver
                            </button>
                        <?php } else { ?>
                            <button class="btn btn-warning" onclick="aktiver()" id="aktiveringsknapp">Aktivér</button>
                        <?php } ?>

                        <button class="btn btn-danger" onclick="vis_slett()" id="slett">Slett</button>

                        <button class="btn btn-info" onclick="neste()" id="neste">Neste person</button>
                        <button class="btn btn-default" onclick="forrige()" id="forrige">Forrige person</button>
                        <button class="btn btn-primary" onclick="vis()" id="commit-res">Lagre resultat</button>
                    </p>

                <?php } ?>

            </div>

            <?php if (!$lista->erFerdig() && !$lista->erArkivert()) { ?>
                <div class="col-lg-6">

                    <h3>Ledige rom</h3>

                    <p><select class="form-control" onchange="leggtilrom(this)" id="rom">
                            <option>- Velg -</option>
                            <?php foreach ($ledige_rom as $rom) { ?>
                                <option value="<?php echo $rom->getId(); ?>"><?php echo $rom->getNavn(); ?> (LEDIG)
                                </option>
                            <?php } ?>

                            <?php foreach ($alle_rom as $rom) {
                                /* @var \intern3\Rom $rom */
                                ?>

                                <option value="<?php echo $rom->getId(); ?>"><?php echo $rom->getNavn(); ?>
                                </option>
                                <?php
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
                </div>


                <?php if (!$lista->erFerdig() && !$lista->erArkivert()) { ?>
                    <div class="col-lg-6">
                        <h3>Legg til beboere</h3>
                        <p>Disse blir lagt til nederst. Kan bare legge til de som ikke er på lista fra før.</p>

                        <button class="btn btn-primary" onclick="vis_beboerlisten()">Velg Beboer(e)</button>


                    </div>

                <?php } ?>


                <table class="table table-responsive table-hover grid" id="sort">

                    <thead>
                    <tr>
                        <th class="index">Nr.</th>
                        <th>Navn</th>
                        <th>Gammelt Rom</th>
                        <th>Nytt Rom</th>
                        <th>Ansiennitet</th>
                        <th>Klassetrinn</th>
                        <th></th>
                        <th></th>
                    </tr>
                    </thead>

                    <tbody>

                    <?php

                    $velgerNr = $lista->getVelgerNr();
                    foreach ($lista->getRekkefolge() as $velger) {
                        /* @var $velger \intern3\StorhybelVelger */
                        $nummer = $velger->getNummer();
                        $klassen = '';
                        if ($nummer == $velgerNr) {
                            $klassen = 'danger';
                        }

                        ?>

                        <tr id="<?php echo $velger->getVelgerId(); ?>" class="<?php echo $klassen; ?>">
                            <td class="index"><?php echo $nummer; ?></td>
                            <td><?php echo $velger->getNavn(); ?></td>
                            <td><?php echo $lista->getFordeling()[$velger->getVelgerId()]->getGammleRomAsString(); ?></td>
                            <td>
                                <?php echo $lista->getFordeling()[$velger->getVelgerId()]->getNyttRomId() !== null
                                    ? $lista->getFordeling()[$velger->getVelgerId()]->getNyttRom()->getNavn()
                                    : ''; ?></td>
                            <td><?php echo $velger->getAnsiennitet(); ?></td>
                            <td><?php echo $velger->getKlassetrinn(); ?></td>
                            <td>
                                <?php if ((in_array($lista->getStatusTekst(), array('Inaktiv', 'Aktiv'))) && $nummer < $velgerNr) { ?>
                                    <button class="btn btn-warning"
                                            onclick="omgjor(<?php echo $velger->getVelgerId(); ?>, <?php echo $velger->getNummer(); ?>)">
                                        Omgjør
                                    </button>
                                <?php } ?>
                            </td>
                            <td>
                                <?php if (!$lista->erFerdig()) { ?>
                                    <button class="btn btn-danger"
                                            onclick="fjernvelger(<?php echo $velger->getVelgerId(); ?>)">Fjern
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

    <div id="modaler">
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

        <div class="modal fade" id="slett-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Slette Storhybellisten</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="rom">
                        <p>
                            Når du trykker på knappen under, vil hele lista slettes for godt. Permanent.
                            Det er ingen vei tilbake. Er du sikker?
                        </p>

                        <p>
                            <button class="btn btn-danger" onclick="slett()">
                                SLETT
                            </button>
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Lukk</button>
                    </div>
                </div>
            </div>
        </div>


        <div class="modal fade" id="beboer-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
             aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Velg beboer(e)</h4>
                    </div>
                    <div class="modal-body" id="beboer">
                        ...
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Lukk</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>

        var markert = <?php echo $lista->getVelgerNr(); ?>;
        var tabellen = document.getElementById("sort");

        function updateMarkert(ny) {
            tabellen.rows[markert].classList.remove('danger');
            var length = tabellen.rows.length;
            if (ny === 0 || ny % length === 0) {
                if (markert - ny < 0) {
                    markert = ny % length + 1;
                } else {
                    markert = length - 1;
                }
            } else {
                markert = ny % length;
            }

            tabellen.rows[markert].classList.add('danger');
        }


        function tilbakemelding(beskjed) {
            document.getElementById("success").style.display = "table";
            document.getElementById("tilbakemelding-text").innerHTML = beskjed;
        };

        var beboer_id;

        function updateDB(beboer_id, nummer) {

            $.ajax({
                type: 'POST',
                url: '?a=utvalg/romsjef/storhybel/liste/oppdater/<?php echo $lista->getId(); ?>',
                data: 'velger_id=' + beboer_id + '&nummer=' + nummer,
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
                    document.getElementById('aktiveringsknapp').onclick = function () {
                        deaktiver();
                    };
                    document.getElementById('aktiveringsknapp').innerText = 'Deaktiver';
                    updateMarkert(1);

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
                    document.getElementById('aktiveringsknapp').onclick = function () {
                        aktiver();
                    };
                    document.getElementById('aktiveringsknapp').innerText = 'Aktiver';
                },
                error: function (req, stat, err) {
                    alert(err);
                }
            });
        }

        function fjernvelger(velger_id) {

            $.ajax({
                type: 'POST',
                url: '?a=utvalg/romsjef/storhybel/liste/fjernvelger/<?php echo $lista->getId(); ?>',
                data: 'velger_id=' + velger_id,
                method: 'POST',
                success: function (data) {
                    $("#" + velger_id).hide();
                    tilbakemelding(data);
                },
                error: function (req, stat, err) {
                    alert(err);
                }
            });

        }

        function omgjor(velger_id, nr) {

            $.ajax({
                type: 'POST',
                url: '?a=utvalg/romsjef/storhybel/liste/omgjor/<?php echo $lista->getId(); ?>',
                data: 'velger_id=' + velger_id,
                method: 'POST',
                success: function (data) {
                    updateMarkert(nr);
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
                    updateMarkert(markert + 1);
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
                    updateMarkert(markert - 1);
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

        function vis_slett() {
            $("#slett-modal").modal("show");
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

        function arkiver() {
            $.ajax({
                type: 'POST',
                url: '?a=utvalg/romsjef/storhybel/liste/arkiver/<?php echo $lista->getId(); ?>',
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

        function vis_beboerlisten() {
            $("#beboer").load('?a=utvalg/romsjef/storhybel/beboerliste/<?php echo $lista->getId(); ?>');
            $("#beboer-modal").modal("show");
        }

        $(document).ready(function() {
            function sortSelect(selElem) {
                var tmpAry = new Array();
                for (var i=0;i<selElem.options.length;i++) {
                    tmpAry[i] = new Array();
                    tmpAry[i][0] = selElem.options[i].text;
                    tmpAry[i][1] = selElem.options[i].value;
                }
                tmpAry.sort();
                while (selElem.options.length > 0) {
                    selElem.options[0] = null;
                }
                for (var i=0;i<tmpAry.length;i++) {
                    var op = new Option(tmpAry[i][0], tmpAry[i][1]);
                    selElem.options[i] = op;
                }
                return;
            }

            sortSelect(document.getElementById('rom'));
        })

    </script>

    <?php
$ru = getrusage();

echo "This process used " . rutime($ru, $rustart, "utime") .
    " ms for its computations<br/>";
echo "It spent " . rutime($ru, $rustart, "stime") .
    " ms in system calls<br/>";
require_once(__DIR__ . '/../../static/bunn.php');