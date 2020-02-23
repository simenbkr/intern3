<?php

require_once(__DIR__ . '/../../static/topp.php');

$df = new \IntlDateFormatter('nb_NO',
    \IntlDateFormatter::MEDIUM, \IntlDateFormatter::NONE,
    'Europe/Oslo');

if (!isset($_SESSION['semester'])) {
    $sem = \intern3\Funk::generateSemesterString(date('Y-m-d'));

    if (strpos('var', $sem)) {
        $_SESSION['semester'] = 'var';
    } else {
        $_SESSION['semester'] = 'host';
    }
}

if ($_SESSION['semester'] == 'frana') {
    $ukeStart = strtotime('now');
    $slutt = \intern3\Funk::getSemesterEnd(\intern3\Funk::generateSemesterString(date('Y-m-d')));
    $ukeSlutt = strtotime($slutt);
} elseif ($_SESSION['semester'] == "var") {
    $ukeStart = strtotime('2 January');
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
    <div class="container">
        <div class="col-lg-12">
            <h1>Regi » Regivakt</h1>
            <hr>

            <p>[ Oversikt ] | [ <a href="?a=regi/regivakt/bytte">Bytte</a> ]</p>

            <hr>
            <p>
                Her kan du melde deg på Regivakter. Klikk på knappene for å se detaljer.
                Du kan melde deg på så lenge det er plass, inntil dagen før vakten er satt.
                Når du har meldt deg på, kan du ikke melde deg av, men du kan bytte på byttemarkedet.
            </p>

            <?php require_once(__DIR__ . '/../../static/tilbakemelding.php'); ?>

            <hr>

            <div class="col-md-5">
                <h3>Dine regivakter:</h3>
                <table class="table table-responsive table-bordered table-condensed">
                    <?php foreach ($mine_vakter as $rv) {
                        /* @var \intern3\Regivakt $rv */
                        ?>
                        <tr>
                            <td><?php echo $rv->getNokkelord(); ?></td>
                            <td>Tidspunkt:</td>
                            <td>
                                <button class="btn btn-info btn-sm" onclick="vis('<?php echo $rv->getId(); ?>')">
                                    <?php echo "{$df->format(strtotime($rv->getDato()))} {$rv->getStartTid()}-{$rv->getSluttTid()}"; ?>
                                </button>
                            </td>
                        </tr>
                    <?php } ?>

                </table>
                <hr>
            </div>

            <div class="col-md-12">

                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">Semester
                        <span class="caret"></span></button>
                    <ul class="dropdown-menu">
                        <li><a href="#" onclick="setVar()">Vår</a></li>
                        <li><a href="#" onclick="setHost()">Høst</a></li>
                        <li><a href="#" onclick="fraNa()">Fra nå til semesterslutt</a></li>
                    </ul>
                </div>

                <hr>
            </div>


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
                                        class="hidden-sm">dag</span></span>&nbsp;<?php echo date('d/m',
                                $ukeStart); ?>
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

                                <?php

                                $lista = \intern3\Regivakt::listeMedDato(date('Y-m-d', $ukeStart + $dag * 86400));
                                if (count($lista) > 0) { ?>

                                    <?php foreach ($lista as $rv) {
                                        if (strtotime($rv->getDato()) - strtotime(date('Y-m-d')) < 86400) {
                                            $rv->setStatusInt(1);
                                            $rv->lagre();
                                        }

                                        $class = 'btn-primary';
                                        switch ($rv->getStatusInt()) {
                                            case '1':
                                                $class = 'btn-default';
                                                break;
                                            case '2':
                                                $class = 'btn-success';
                                                break;
                                            case '3':
                                                $class = 'btn-danger';
                                                break;
                                        }
                                        ?>

                                        <button class="btn <?php echo $class; ?> btn-sm navbar-btn"
                                                onclick="vis(<?php echo $rv->getId(); ?>)">
                                            Påmeldte: <?php $str = "{$rv->getAntallPameldte()}/{$rv->getAntall()} - {$rv->getNokkelord()}";
                                            echo substr($str, 0, 20);
                                            ?>
                                        </button>

                                    <?php }
                                }
                                  ?>

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

        function vis(id) {
            $("#mod").load("?a=regi/regivakt/vis/" + id);
            $("#modal-modal").modal("show");
        }

        function setVar() {
            $.ajax({
                cache: false,
                type: 'POST',
                url: '?a=vakt/setvar',
                success: function (data) {
                    location.reload();
                }
            });
        }

        function setHost() {
            $.ajax({
                cache: false,
                type: 'POST',
                url: '?a=vakt/sethost',
                success: function (data) {
                    location.reload();
                }
            });
        }

        function fraNa() {
            $.ajax({
                cache: false,
                type: 'POST',
                url: '?a=vakt/setna',
                success: function (data) {
                    location.reload();
                }
            });
        }
    </script>

<?php

require_once(__DIR__ . '/../../static/bunn.php');


