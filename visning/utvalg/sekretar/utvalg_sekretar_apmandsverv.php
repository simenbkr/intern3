<?php
require_once(__DIR__ . '/../topp_utvalg.php');

?>
<script>
    function fjern(beboerId, vervId) {
        $.ajax({
            type: 'POST',
            url: '?a=utvalg/sekretar/apmandsverv',
            data: 'fjern=' + beboerId + '&verv=' + vervId,
            method: 'POST',
            success: function (data) {
                window.location.reload();
            },
            error: function (req, stat, err) {
                alert(err);
            }
        });
    }

    function modal() {
        var vervId = $(this).attr('data-target');
        var vervNavn = $(this).attr('data-name');
        $.ajax({
            cache: false,
            type: 'POST',
            url: '?a=utvalg/sekretar/apmandsverv_modal',
            data: 'vervId=' + vervId + '&vervNavn=' + vervNavn,
            success: function (data) {
                $('#modal-' + vervId).html(data);
                $('#' + vervId + '-åpmand').modal('show');
                $('#' + VervId + '-åpmand').on('hidden.bs.modal', function () {
                    $('#modal-' + vervId).html(' ');
                });
            }
        });
    }
</script>

<div class="container">

    <div class="col-md-6">
        <h1>Utvalget &raquo; Sekretær &raquo; Åpmandsverv</h1>

        <hr>
    
        <?php include(__DIR__ . '/../../static/tilbakemelding.php'); ?>
    </div>

    <div class="col-md-6">
        <h3>Opprett åpmandsverv</h3>
        <form action="" method="post">
            <table class="table table-bordered table-responsive">
                <tr>
                    <td>Navn:</td>
                    <td><input type="text" name="navn" class="form-control" value=""></td>
                </tr>
                <tr>
                    <td>Beskrivelse:</td>
                    <td><textarea rows="2" cols="20" name="beskrivelse" class="form-control"></textarea></td>
                </tr>
                <tr>
                    <td>Regitimer:</td>
                    <td><input type="number" name="regitimer" class="form-control" value=""></td>
                </tr>
                <tr>
                    <td>E-post:</td>
                    <td><input type="text" name="regitimer" class="form-control" value=""></td>
                </tr>
                <tr>
                    <td>Utvalg?</td>
                    <td><input type="checkbox" name="utvalg" value="1"> <br></td>
                </tr>

                <tr>
                    <td></td>
                    <td><input class="btn btn-primary" type="submit" value="Legg til"></td>
                </tr>
            </table>
        </form>
    </div>

    <div class="col-md-12">
        <table class="table table-bordered">
            <tr>
                <th>Åpmandsverv</th>
                <th>Åpmand/åpmend</th>
                <th>Legg til/endre</th>
                <th>Epost</th>
            </tr>
            <?php

            foreach ($vervListe as $verv) {
                ?>
                <div id="<?php echo $verv->getId(); ?>">
                    <tr id="<?php echo $verv->getId(); ?>">
                        <td>
                            <a href="?a=utvalg/sekretar/apmandsverv/<?php echo $verv->getId(); ?>"><?php echo $verv->getNavn(); ?></a>
                            <a href="?a=utvalg/sekretar/apmandsverv/<?php echo $verv->getId(); ?>">(endre)</a></td>
                        <td><?php
                            $i = 0;
                            foreach ($verv->getApmend() as $apmand) {
                                if ($i++ > 0) {
                                    echo ', ';
                                }
                                echo '<a href="?a=beboer/' . $apmand->getId() . '">' . $apmand->getFulltNavn() . '</a>'; ?>
                                <button onclick="fjern(<?php echo $apmand->getId(); ?>,<?php echo $verv->getId(); ?>)">
                                    &#x2718;
                                </button>
                            <?php } ?>
                        </td>
                        <td>
                </div>
                <div>
                    <input type="button" class="btn btn-sm btn-info" value="Legg Til" onclick="modal.call(this)"
                           data-target="<?php echo $verv->getId(); ?>" data-name="<?php echo $verv->getNavn(); ?>">
                    <div id="modal-<?php echo $verv->getId(); ?>">
                    </div>
                </div>
                </td>
                <td><?php
                    $epost = $verv->getEpost();
                    if ($verv == null) {
                        echo ' ';
                    } else {
                        echo '<a href="mailto:' . $epost . '">' . $epost . '</a>';
                    }
                    ?></td></tr>
                <?php
            }
            ?>
        </table>
    </div>
</div>

<?php

require_once(__DIR__ . '/../../static/bunn.php');

?>
