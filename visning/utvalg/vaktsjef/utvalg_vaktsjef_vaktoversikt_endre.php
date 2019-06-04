<?php
require_once(__DIR__ . '/../topp_utvalg.php');

/* @var $beboer \intern3\Beboer */

$data = array();
$i = 1;
foreach($vakter as $vakt) {
    /* @var $vakt \intern3\Vakt */

    switch ($vakt->getVakttype()) {
        case 1:
            $tid = strtotime($vakt->getDato() . '+ 1hour');
            break;
        case 2:
            $tid = strtotime($vakt->getDato() . '+ 7hours');
            break;
        case 3:
            $tid = strtotime($vakt->getDato() . '+ 13hours');
            break;
        case 4:
            $tid = strtotime($vakt->getDato() . '+ 19hours');
    }

    $data[] = array(
        'id' => $i++,
        'content' => $vakt->getVakttype() . '. vakt',
        'start' => date('Y-m-d H:i:s', $tid),
        //'end' => date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s', $tid) . '+ 6hours'))
    );

}

$df = new IntlDateFormatter('nb_NO',
    IntlDateFormatter::TRADITIONAL, IntlDateFormatter::NONE,
    'Europe/Oslo');

?>
    <script src="js/vis.min.js"></script>
    <link href="css/vis.min.css" rel="stylesheet" type="text/css" />
    <div class="container">
        <h1>Utvalget » Vaktsjef » Vaktoversikt » Endre vaktantall for <?php echo $beboer->getFulltNavn(); ?></h1>
        <hr>

        <?php include(__DIR__ . '/../../static/tilbakemelding.php'); ?>

        <?php if($beboer->getBruker()->vaktAdvarsel()) { ?>
        <div class="alert alert-danger">
                <?php echo $beboer->getBruker()->advarselArsak(); ?>
        </div>
        <?php } ?>

        <div class="col-md-6">
            <h2>Endre antall vakter:</h2>
            <form action="" method="post">
                <table class="form table table-responsive table-bordered">
                    <tr>
                        <th>Antall vakter:</th>
                        <td><input class="form-control" autofocus type="number" max="20" name="antall"
                                   placeholder="<?php echo $beboer->getBruker()->antallVakterSkalSitte(); ?>"</td>
                    </tr>
                    <tr>
                        <th>Semester:</th>
                        <td>
                            <select name="semester">
                                <?php
                                foreach ($options as $option) {
                                    $tmp = str_replace("a", "å", $option);
                                    $tmp = str_replace("o", "ø", $tmp);
                                    $tmp = ucfirst(str_replace("-", " ", $tmp));
                                    ?>
                                    <option value="<?php echo $option; ?>"><?php echo $tmp; ?></option>
                                <?php }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <td></td>
                    <td><input type="submit" class="btn btn-primary" value="Endre"></td>
                    </tr>
                </table>
            </form>
        </div>

        <div class="col-md-6">
            <h2>Vakter oppsatt:</h2>
            <table class="table table-bordered table-responsive table-striped">
                <tr>
                    <td><b>Type</b></td>
                    <td><b>Dato</b></td>
                    <td><b>Dag</b></td>
                    <td><b>Kjip?</b></td>
                </tr>
                <?php
                foreach ($vakter as $vakt) {
                    /* @var $vakt \intern3\Vakt */
                    if ($vakt->erFerdig()) {
                        continue;
                    }
                    ?>
                    <tr>
                        <td><?php echo $vakt->getVaktType(); ?>. vakt</td>
                        <td><?php echo $vakt->getDato(); ?></td>
                        <td><?php echo ucfirst(explode(' ', $df->format(strtotime($vakt->getDato())))[0]); ?></td>
                        <td><?php echo $vakt->erKjip() ? 'Ja' : 'Nei'; ?></td>
                    </tr>
                    <?php
                }
                ?>
            </table>

            <h2>Vakter sittet:</h2>
            <table class="table table-bordered table-responsive table-striped">
                <tr>
                    <td><b>Type</b></td>
                    <td><b>Dato</b></td>
                    <td><b>Dag</b></td>
                    <td><b>Kjip?</b></td>
                </tr>
                <?php
                foreach ($vakter as $vakt) {
                    /* @var $vakt \intern3\Vakt */
                    if (!$vakt->erFerdig()) {
                        continue;
                    }
                    ?>
                    <tr>
                        <td><?php echo $vakt->getVaktType(); ?>. vakt</td>
                        <td><?php echo $vakt->getDato(); ?></td>
                        <td><?php echo ucfirst(explode(' ', $df->format(strtotime($vakt->getDato())))[0]); ?></td>
                        <td><?php echo $vakt->erKjip() ? 'Ja' : 'Nei'; ?></td>
                    </tr>
                    <?php
                }
                ?>
            </table>

        </div>

        <div id="visual"></div>


    </div>

    <script>

        var container = document.getElementById('visual');
        var data = <?php echo json_encode($data); ?>;
        var timeline = new vis.Timeline(container, data, {});

    </script>


<?php
require_once(__DIR__ . '/../../static/bunn.php');
?>