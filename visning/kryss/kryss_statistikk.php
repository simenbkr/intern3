<?php

require_once(__DIR__ . '/../static/topp.php');


$drikke_assoc = array();
foreach ($drikker as $drikke) {
    $drikke_assoc[$drikke->getNavn()] = $drikke->getId();
}
$df = new \IntlDateFormatter('nb_NO',
    \IntlDateFormatter::TRADITIONAL, \IntlDateFormatter::SHORT,
    'Europe/Oslo');


?>
    <script src="js/visjs5-1.js"></script>
    <link href="css/visjs5-1.css" rel="stylesheet" type="text/css"/>
    <script type="text/javascript" src="js/dataTables.js"></script>
    <link rel="stylesheet" type="text/css" href="css/dataTables.css"/>

    <div class="container">
        <h1>Kryss &raquo; Statistikk</h1>

        [ <a href="?a=kryss">Kryss historikk</a> ] | [ Kryssestatistikk ] | [ <a href="?a=kryss/prisliste">Prisliste</a> ]

        <hr>

        <div class="col-lg-12">

            <div class="col-lg-6">
                Forrige periode, som var fra <?php echo $df->format(strtotime($periode->getStart())); ?> til
                <?php echo $df->format(strtotime($periode->getSlutt())); ?>
                ble det krysset:

                <table class="table table-bordered" id="tabellen">
                    <thead>
                        <th>Drikke</th>
                        <th>Antall</th>
                    </thead>
                    <tbody>
                        <?php foreach ($totalt as $navn => $antall) {
                            if($antall < 1) {
                                continue;
                            }

                            $sum += $antall;
                            ?>
                            <tr>
                                <td><?php echo $navn; ?></td>
                                <td><?php echo $antall; ?></td>
                            </tr>
                        <?php } ?>

                        <tr>
                            <td><b>Totalt</b></td>
                            <td><b><?php echo $sum > 0 ? $sum : 0; ?></b></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-lg-12">
                <div id="visu"></div>
            </div>
        </div>
    </div>

    <script>
        var container = document.getElementById('visu');
        var groups = new vis.DataSet();
        var items = [];

        <?php foreach ($drikker as $drikke) {
            echo "groups.add({id:{$drikke->getId()}, content:'{$drikke->getNavn()}'});\n\t";
        }
        ?>

        <?php

        foreach ($dato_liste as $dato => $drikke_array) {
            foreach ($drikke_array as $navn => $antall) {
                if ($antall < 1) {
                    continue;
                }
                echo "items.push({x: '$dato', y: $antall, group: $drikke_assoc[$navn], label: {content: '$antall'}});\n\t";
            }
        }

        ?>

        var dataset = new vis.DataSet(items);
        var options = {
            style: 'bar',
            stack: true,
            legend: {
                enabled: true,
            },
            drawPoints: false,
            //drawPoints: {
            //    size: 0,
            //},
            dataAxis: {
                icons: true,
            },
            orientation: 'top',
            timeAxis: {
                scale: 'day',
                step: 1
            },
            zoomable: false,
            moveable: false,
            showMinorLabels: true,
            showMajorLabels: true,
        };

        var graph2d = new vis.Graph2d(container, items, groups, options);

    </script>

<?php

require_once(__DIR__ . '/../static/bunn.php');
