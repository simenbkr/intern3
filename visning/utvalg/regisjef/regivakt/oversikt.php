<?php

require_once(__DIR__ . '/../../topp_utvalg.php');

if ($_SESSION['semester'] == "var") {
    $ukeStart = strtotime('8 January');
    $ukeSlutt = strtotime('1 July');
    if (date('W', $ukeStart) == 53) {
        $ukeStart = strtotime('next week', $ukeStart);
    }
} else {
    $ukeStart = strtotime('5 August');
    $ukeSlutt = strtotime('1 January + 1 year');
}
$ukeStart = strtotime('last week', $ukeStart);

?>
    <script src="js/moment.min.js"></script>
    <script src="js/bootstrap-datetimepicker.min.js"></script>
    <div class="container">
        <div class="col-lg-12">
            <h1>Regisjef » Regivakt</h1>

            <hr>

            <p></p>
            <?php require_once(__DIR__ . '/../../../static/tilbakemelding.php'); ?>


            <table class="table-bordered table table-responsive">

                <?php foreach (range(date('W', $ukeStart), date('W', $ukeSlutt)) as $uke) {
                    $ukeStart = strtotime('+1 week', $ukeStart);
                    ?>

                    <tr>
                        <th style="width:5.5%;"><span
                                    class="hidden-sm hidden-xs">Uke&nbsp;</span><?php echo intval(date('W',
                                $ukeStart)); ?>
                        </th>
                        <th style="width:13.5%;">M<span class="hidden-xs">an<span
                                        class="hidden-sm">dag</span></span>&nbsp;<?php echo date('d/m', $ukeStart); ?>
                        </th>
                        <th style="width:13.5%;">T<span class="hidden-xs">ir<span
                                        class="hidden-sm">sdag</span></span>&nbsp;<?php echo date('d/m',
                                strtotime('+1 day', $ukeStart)); ?></th>
                        <th style="width:13.5%;">O<span class="hidden-xs">ns<span
                                        class="hidden-sm">dag</span></span>&nbsp;<?php echo date('d/m',
                                strtotime('+2 day', $ukeStart)); ?></th>
                        <th style="width:13.5%;">T<span class="hidden-xs">or<span
                                        class="hidden-sm">sdag</span></span>&nbsp;<?php echo date('d/m',
                                strtotime('+3 day', $ukeStart)); ?></th>
                        <th style="width:13.5%;">F<span class="hidden-xs">re<span
                                        class="hidden-sm">dag</span></span>&nbsp;<?php echo date('d/m',
                                strtotime('+4 day', $ukeStart)); ?></th>
                        <th style="width:13.5%;">L<span class="hidden-xs">ør<span
                                        class="hidden-sm">dag</span></span>&nbsp;<?php echo date('d/m',
                                strtotime('+5 day', $ukeStart)); ?></th>
                        <th style="width:13.5%;">S<span class="hidden-xs">øn<span
                                        class="hidden-sm">dag</span></span>&nbsp;<?php echo date('d/m',
                                strtotime('+6 day', $ukeStart)); ?></th>
                    </tr>

                    <tr>
                        <td><span class="hidden-sm hidden-xs"><b>Regivakt</b></span><br>&nbsp;</td>

                        <?php foreach (range(0, 6) as $dag) { ?>
                            <td>
                                <?php foreach (\intern3\Regivakt::listeMedDato(date('Y-m-d',
                                    $ukeStart + $dag * 86400)) as $rv) { ?>

                                    <button class="btn btn-warning btn-sm" onclick="administrer(<?php echo $rv->getId(); ?>)">
                                    Antall: <?php echo $rv->getAntall() . ' - ' . $rv->getNokkelord(); ?>
                                    </button>
                                    <br>

                                <?php }
                                  ?>
                                <button class="btn btn-primary btn-sm"
                                        onclick="vis('<?php echo date('Y-m-d', $ukeStart + $dag * 86400); ?>')">
                                    Legg til vakt
                                </button>
                            </td>
                        <?php }
                          ?>

                    </tr>

                <?php }
                  ?>

        </div>
    </div>


    <div class="modal fade" id="modal-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="modal-tittel"></h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="mod">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Lukk</button>
                </div>
            </div>
        </div>
    </div>


    <script>

        function vis(dato) {
            $("#mod").load("?a=utvalg/regisjef/regivakt/vis/" + dato);
            $("#modal-modal").modal("show");
        }

        function administrer(id) {
            $("#mod").load("?a=utvalg/regisjef/regivakt/administrer/" + id);
            $("#modal-modal").modal("show");
        }
    </script>
<?php

require_once(__DIR__ . '/../../../static/bunn.php');
