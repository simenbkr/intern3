<?php
require_once('topp_journal.php');
require_once(__DIR__ . '/../static/topp.php');

$beboere_med_depositum = array();

foreach ($beboere as $beboer) {
    if ($beboer->harAlkoholdepositum()) {
        $beboere_med_depositum[] = $beboer;
    }
}
?>
<link rel="stylesheet" type="text/css" href="css/dataTables.css"/>
<script type="text/javascript" src="js/dataTables.js"></script>
<script>

    var rad = 0;
    <?php
    if(isset($_SESSION['scroll']) && is_numeric($_SESSION['scroll'])){ ?>
    rad = <?php echo $_SESSION['scroll']; ?>;
    <?php
    }
    ?>

    $(document).ready(function () {
        var table = $('#tabellen').DataTable({
            "paging": false,
            "searching": false,
            "scrollY": "500px",
            "scrollCollapse": true
        });
        if (rad != undefined && rad != 0) {
            var $scrollBody = $(table.table().node()).parent();

            $scrollBody.scrollTop($("#" + rad).offset().top - $scrollBody.height());

        }

    });

</script>

<div class="container">
    <h1>Journal » Krysseliste</h1>
    <hr>

    <?php require_once(__DIR__ . '/../static/tilbakemelding.php'); ?>

    <div class="col-lg-12">

        <table id="tabellen" class="table table-bordered table-responsive tableSection" data-toggle="table">
            <thead>
            <tr>
                <th style="width: 25%;" data-sortable="true">Navn</th>
                <?php foreach ($drikke as $drikken) {
                    if ($denne_vakta->drukketDenneVakta($drikken->getId()) || $drikken->getAktiv()) { ?>
                        <th style="width: 15%;" data-sortable="true"><?php echo $drikken->getNavn(); ?></th>
                        <?php
                    }
                    ?>

                <?php } ?>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($beboere_med_depositum as $beboer) { ?>
                <tr id="<?php echo $beboer->getId(); ?>">
                    <td style="width: 25%;"><a
                                href="?a=journal/kryssing/<?php echo $beboer->getId(); ?>"><?php echo $beboer->getFulltNavn(); ?></a>
                    </td>
                    <?php foreach ($drikke as $drikken) {
                        if ($denne_vakta->drukketDenneVakta($drikken->getId()) || $drikken->getAktiv()) { ?>
                            <td style="width: 15%;"><?php echo $krysseliste[$beboer->getId()][$drikken->getNavn()]; ?></td>
                        <?php }
                    }
                    ?>
                </tr>
                <?php
            } ?>
            </tbody>
        </table>
        <h2><a href="?a=journal">TILBAKE</a></h2>
        <hr>
    </div>
</div>
<?php

require_once(__DIR__ . '/../static/bunn.php');