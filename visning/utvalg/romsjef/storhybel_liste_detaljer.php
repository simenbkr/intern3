<?php

require_once(__DIR__ . '/../topp_utvalg.php');

/* @var $lista \intern3\Storhybelliste */

?>
    <div class="container">
        <div class="col-lg-12">
            <h1>Utvalget &raquo; Romsjef &raquo; StorhybellisteLISTE &raquo; <?php echo $lista->getNavn(); ?></h1>

            <hr>

            <div class="col-lg-6">

                <p>Status: <?php echo $lista->erAktiv() ? 'Aktiv' : 'Inaktiv'; ?></p>

                <p>
                    <button class="btn btn-info">Aktiver</button>
                    <button class="btn btn-warning">Deaktiver</button>
                </p>

            </div>

            <div class="col-lg-6">

                <h3>Ledige rom</h3>
                <button class="btn btn-primary">Legg til</button>
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

                        <tr>
                            <td><?php echo $rom->getNavn(); ?></td>
                            <td><?php echo $rom->getType()->getNavn(); ?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>

            <div class="col-lg-12">

                <h3>Beboerliste med ansiennitet</h3>
                <p>(drag-and-drop for å endre)</p>

                <div class="alert alert-success fade in" id="success" style="margin: auto; margin-top: 5%; display:none">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <p id="tilbakemelding-text"></p>
                </div>

                <table class="table table-responsive table-hover grid" id="sort">

                    <thead>
                    <tr>
                        <th class="index">No.</th>
                        <th>Navn</th>
                        <th>Rom</th>
                        <th>Ansiennitet</th>
                        <th>Klassetrinn</th>
                        <th>Plass</th>
                    </tr>
                    </thead>

                    <tbody>

                    <?php foreach ($lista->getRekkefolge() as $nummer => $beboer) {
                        /* @var $beboer \intern3\Beboer */
                        ?>

                        <tr id="<?php echo $beboer->getId(); ?>">
                            <td class="index"><?php echo $nummer; ?></td>
                            <td><?php echo $beboer->getFulltNavn(); ?></td>
                            <td><?php echo $beboer->getRom()->getNavn(); ?></td>
                            <td><?php echo $beboer->getAnsiennitet(); ?></td>
                            <td><?php echo $beboer->getKlassetrinn(); ?></td>
                            <td><button class="btn btn-danger">Fjern</button></td>
                        </tr>

                        <?php
                    } ?>
                    </tbody>


                </table>


            </div>


        </div>
    </div>

<script>

    function tilbakemelding(beskjed){
        document.getElementById("success").style.display = "table";
        document.getElementById("tilbakemelding-text").innerHTML = beskjed;
    }

    var beboer_id;

    function updateDB(beboer_id, nummer) {

        $.ajax({
            type: 'POST',
            url: '?a=utvalg/romsjef/storhybel/liste/oppdater/<?php echo $lista->getId(); ?>',
            data: 'beboer_id=' + beboer_id +'&nummer='+ nummer,
            method: 'POST',
            success: function (data) {
                tilbakemelding(data);
            },
            error: function (req, stat, err) {
                alert(err);
            }
        });

    }

    var fixHelperModified = function(e, tr) {
            var $originals = tr.children();
            var $helper = tr.clone();
            $helper.children().each(function(index) {
                $(this).width($originals.eq(index).width())
            });

            beboer_id = tr[0].id;

            return $helper;
        },
        updateIndex = function(e, ui) {
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

</script>

<?php

require_once(__DIR__ . '/../../static/bunn.php');

