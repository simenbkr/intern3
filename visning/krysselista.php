<?php
require_once('topp.php');

$length = count($beboere);
$beboere_1 = array_slice($beboere, 0, $length / 2);
$beboere_2 = array_slice($beboere, $length / 2);

?>

    <hr><br/><h1>Krysseliste</h1>
    <div class="container-fluid">
        <div class="border row">

            <div class="border col-md-6">
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
            <div class="border col-md-6">
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
            </div>


        </div>
    </div>
    <hr>
<?php

require_once('bunn.php');

?>