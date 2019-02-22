<?php
require_once(__DIR__ . '/../static/topp.php');
$dag_array = array(
    0 => 'Torsdag',
    1 => 'Fredag',
    2 => 'Lørdag'
);

$dag_arrayv2 = array(
    0 => 'torsdag',
    1 => 'fredag',
    2 => 'lordag'
);

if (is_array($max_gjeste_count)) {
    $ledige = $max_gjeste_count[$dag_arrayv2[$dag_tall]] - $gjeste_count;
} else {
    $ledige = $max_gjeste_count - $gjeste_count;
}

$side_tittel = "HELGA » $dag_array[$dag_tall]";
switch ($dag_tall) {
    case 0:
        $undertittel = "[ Torsdag ] [ <a href='?a=helga/fredag'>Fredag</a> ] [ <a href='?a=helga/lordag'>Lørdag</a> ]";
        break;
    case 1:
        $undertittel = "[ <a href='?a=helga/torsdag'>Torsdag</a> ] [ Fredag ] [ <a href='?a=helga/lordag'>Lørdag</a> ]";
        break;
    case 2:
        $undertittel = "[ <a href='?a=helga/torsdag'>Torsdag</a> ] [ <a href='?a=helga/fredag'>Fredag</a> ] [ Lørdag ]";
        break;
    default:
        $undertittel = "[ Torsdag ] [ <a href='?a=helga/fredag'>Fredag</a> ] [ <a href='?a=helga/lordag'>Lørdag</a> ]";
}

?>
    <script>

        function fjern(id, dag) {
            $.ajax({
                type: 'POST',
                url: '?a=helga/<?php echo $dag_arrayv2[$dag_tall];?>',
                data: 'fjern=fjern&gjestid=' + id + "&dag=" + dag,
                method: 'POST',
                success: function (html) {
                    document.getElementById(id).remove();
                    $('.container').load(document.URL + ' .container');
                },
                error: function (req, stat, err) {
                    alert(err);
                }
            });
        }

        function add(i, dag) {
            var navn = document.getElementById('navn-' + i).value;
            var epost = document.getElementById('epost-' + i).value;

            if (navn.length > 1 && epost.length > 2 && epost.includes('@')) {
                $.ajax({
                    type: 'POST',
                    url: '?a=helga/<?php echo $dag_arrayv2[$dag_tall];?>',
                    data: 'add=' + dag + '&navn=' + navn + '&epost=' + epost,
                    method: 'POST',
                    success: function (html) {
                        document.getElementById('add-' + i).remove();
                        $('#lista').load(document.URL + ' #lista');
                    },
                    error: function (req, stat, err) {
                        alert(err);
                    }
                });
            }
        }

        function addAllDays(i) {
            var navn = document.getElementById('navn-' + i).value;
            var epost = document.getElementById('epost-' + i).value;

            if (navn.length > 1 && epost.length > 2 && epost.includes('@')) {

                for (var j = 0; j < 3; j++) {

                    $.ajax({
                        type: 'POST',
                        url: '?a=helga/<?php echo $dag_arrayv2[$dag_tall];?>',
                        data: 'add=' + j + '&navn=' + navn + '&epost=' + epost,
                        method: 'POST',
                        success: function (html) {
                            document.getElementById('add-' + i).remove();
                            $('#lista').load(document.URL + ' #lista');
                        },
                        error: function (req, stat, err) {
                            alert(err);
                        }
                    });
                }
            }

        }

        function add_all_all_days() {
            console.log("Hei. Jeg er også Håvard Ola-knappen. Jeg liker knapper. Jeg liker Javascript.")
            var max = <?php echo $ledige; ?>;
            var dag = <?php echo $dag_tall; ?>;
            for (var i = 0; i < max; i++) {
                addAllDays(i);
            }

        }

        function add_all() {
            console.log("Hei. Jeg er Håvard Ola-knappen. God Helg(a)!");
            var max = <?php echo $ledige; ?>;
            var dag = <?php echo $dag_tall; ?>;
            for (var i = 0; i < max; i++) {
                add(i, dag);
            }

        }

        function send_epost(id) {
            $.ajax({
                type: 'POST',
                url: '?a=helga/<?php echo $dag_arrayv2[$dag_tall];?>',
                data: 'send=send&gjestid=' + id,
                method: 'POST',
                success: function (html) {
                    document.getElementById(id).remove();
                    //$('.container').load(document.URL + ' .container');
                    $(".container").replaceWith($('.container', $(html)));
                },
                error: function (req, stat, err) {
                    alert(err);
                }
            });
        }

        function vis(id) {
            $("#gjest").load("?a=helga/gjestmodal/" + id);
            $("#gjest-modal").modal("show");
        }
    </script>
    <div class="container">
        <?php
        if (isset($VisError)) {
            ?>
            <div class="alert alert-danger fade in" id="success" style="display:table; margin: auto; margin-top: 5%">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                Noe gikk galt. Hva gjorde du?!
            </div>
            <?php
            unset($VisError);
        }
        if (isset($epostSendt)) {
            ?>
            <div class="alert alert-success fade in" id="success" style="display:table; margin: auto; margin-top: 5%">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                Epost ble sendt!
            </div>
            <?php
            unset($epostSendt);
        }
        ?>

        <div class="row">
            <h1><?php echo $side_tittel; ?></h1>
            <h3><?php echo $undertittel; ?></h3>
            <hr>

            <?php require_once(__DIR__ . '/../static/tilbakemelding.php'); ?>

            <p>Her kan du invitere dine venner til HELGA!</p>
            <div class="col-lg-6">
                <button class="btn btn-default" onclick="add_all()">Legg til alle</button>
                <button class="btn btn-warning" onclick="add_all_all_days()">Legg til alle, alle dager</button>
                <hr>
                <?php
                for ($i = 0; $i < $ledige; $i++) {
                    ?>
                    <div class="formen">
                        <table class="table table-bordered table-responsive">
                            <tr id="add-<?php echo $i; ?>">
                                <td>Navn:
                                    <input id="navn-<?php echo $i; ?>" type="text" name="navn" value=""
                                           class="form-control"/></td>
                                <td>Epost:
                                    <input id="epost-<?php echo $i; ?>" type="text" name="epost" value=""
                                           class="form-control"/></td>
                                <td>
                                    <button class="btn btn-primary"
                                            onclick="add(<?php echo $i; ?>, <?php echo $dag_tall; ?>)">Legg til
                                    </button>
                                </td>
                                <td>
                                    <button class="btn btn-info" onclick="addAllDays(<?php echo $i; ?>)">
                                        Legg til alle dager
                                    </button>

                                </td>
                            </tr>
                        </table>
                    </div>
                <?php } ?>
            </div>

            <div class="col-lg-6" id="lista">

                <table class="table table-bordered table-responsive">
                    <?php
                    foreach ($beboers_gjester as $gjest) {
                        ?>
                        <tr id="<?php echo $gjest->getId(); ?>">
                            <td>Navn: <?php echo $gjest->getNavn(); ?></td>
                            <td>Epost: <?php echo $gjest->getEpost(); ?></td>
                            <td><input class="btn btn-primary" type="submit" value="Slett"
                                       onclick="fjern(<?php echo $gjest->getId(); ?>,<?php echo $dag_tall; ?>)"></td>
                            <?php if ($gjest->getSendt() == 0) { ?>
                                <td>
                                    <button class="btn btn-warning" onclick="vis(<?php echo $gjest->getId(); ?>)">Endre</button>

                                    <input class="btn btn-info" type="submit" value="Send"
                                           onclick="send_epost(<?php echo $gjest->getId(); ?>)">
                                </td>
                            <?php } else { ?>
                                <td>
                                    <button class="btn btn-info disabled">Send</button>
                                </td>
                            <?php } ?>
                        </tr>
                    <?php } ?>

            </div>

        </div>


        <div class="modal fade" id="gjest-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">Endre på gjest</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="gjest">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Lukk</button>
                    </div>
                </div>
            </div>
        </div>

<?php

require_once(__DIR__ . '/../static/bunn.php');