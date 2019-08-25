<?php

require_once(__DIR__ . '/../static/topp.php');


$drikke_assoc = array();
foreach ($drikker as $drikke) {
    $drikke_assoc[$drikke->getNavn()] = $drikke->getId();
}


?>

    <script src="js/visjs5-1.js"></script>
    <link href="css/visjs5-1.css" rel="stylesheet" type="text/css"/>

    <div class="container">
        <h1>Kryss &raquo; Statistikk</h1>

        <hr>

        <div class="col-lg-12">


            <div class="col-lg-6">

                Forrige periode, som var fra <?php echo $periode->getStart(); ?> til <?php echo $periode->getSlutt(); ?> ble det krysset:
                <ul>
                    <?php foreach ($totalt as $navn => $antall) {
                        if ($antall < 1) {
                            continue;
                        }
                        $sum += $antall;
                        ?>
                        <li><?php echo "$navn: $antall"; ?></li>

                    <?php }
                      ?>

                    <li><b>Totalt: <?php echo $sum > 0 ? $sum : 0; ?> flasker/enheter/ting</b></li>
                </ul>


            </div>

            <div class="col-lg-6">

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

    foreach($dato_liste as $dato => $drikke_array) {

        foreach($drikke_array as $navn => $antall) {

            if($antall < 1) {
                continue;
            }


            echo "items.push({x: '$dato', y: $antall, group: $drikke_assoc[$navn], label: {content: '$antall'}});\n\t";
        }

    }

    ?>

    var dataset = new vis.DataSet(items);
    var options = {
        style:'bar',
        stack:true,
        legend: {
            enabled: true,
        },
        drawPoints: {
            size: 0,
        },
        dataAxis: {
            icons:true,
        },
        orientation:'top',
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
