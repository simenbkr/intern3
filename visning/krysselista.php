<?php
require_once ('topp_journal.php');
require_once('topp.php');

$beboere_med_depositum = array();

foreach($beboere as $beboer){
    if($beboer->harAlkoholdepositum()){
        $beboere_med_depositum[] = $beboer;
    }
}
$length = count($beboere_med_depositum);
$beboere_1 = array_slice($beboere_med_depositum, 0, $length / 2);
$beboere_2 = array_slice($beboere_med_depositum, $length / 2);
?>
    <script>
        $(document).ready(function(){
            $('#tabellen').DataTable({
                "paging": false,
                "searching": false
            });

        });
    </script>
    <link rel="stylesheet" type="text/css" href="css/dataTables.css"/>
    <script type="text/javascript" src="js/dataTables.js"></script>
    <hr><br/><h1>Krysseliste</h1>
    <div class="container-fluid">
        <h2><a href="javascript:history.back()">TILBAKE</a></h2>

        <?php /*<div class="border row">

            <div class="border col-md-3">
                <table id="tabell" class="table table-bordered table-responsive" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th>Navn</th>
                        <th>TBD</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($beboere_1 as $beboer) {

                        echo "<tr>";
                        echo "<td>" . "<a href='?a=journal/kryssing/" . $beboer->getId() . "'>" . $beboer->getFulltNavn() . "</a></td>";
                        echo "<td>TBD</td>";
                        echo "</tr>";
                    }

                    ?>
                    </tbody>
                    </table></div>
            <div class="border col-md-3">
                <table id="tabell" class="table table-bordered table-responsive" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th>Navn</th>
                        <th>TBD</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($beboere_2 as $beboer) {

                        echo "<tr>";
                        echo "<td>" . "<a href='?a=journal/kryssing/" . $beboer->getId() . "'>" . $beboer->getFulltNavn() . "</a></td>";
                        echo "<td>TBD</td>";
                        echo "</tr>";
                    }

                    ?>
                    </tbody>
                </table>
            </div> */ ?>
        <table id="tabellen" class="table table-bordered table-responsive" data-toggle="table">
            <thead>
            <tr>
                <th data-sortable="true">Navn</th>
                <th data-sortable="true">Øl</th>
                <th data-sortable="true">Cider</th>
                <th data-sortable="true">Carlsberg</th>
                <th data-sortable="true">Rikdom</th>
                <th data-sortable="true">Pant</th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach($beboere_med_depositum as $beboer){ ?>
                <tr>
                    <td><a href="?a=journal/kryssing/<?php echo $beboer->getId(); ?>"><?php echo $beboer->getFulltNavn(); ?></a></td>
                    <td><?php echo $krysseliste[$beboer->getId()]['Øl'];?></td>
                    <td><?php echo $krysseliste[$beboer->getId()]['Cider'];?></td>
                    <td><?php echo $krysseliste[$beboer->getId()]['Carlsberg'];?></td>
                    <td><?php echo $krysseliste[$beboer->getId()]['Rikdom'];?></td>
                    <td><?php echo $krysseliste[$beboer->getId()]['Pant'];?></td>
                </tr>
                <?php
            } ?>
            </tbody>
        </table>
            <h2><a href="?a=journal">TILBAKE</a></h2>

        </div>
    </div>
    <hr>
<?php

require_once('bunn.php');

?>
