<?php
require_once('topp_journal.php');
require_once('topp.php');

$beboere_med_depositum = array();

foreach ($beboere as $beboer) {
    if ($beboer->harAlkoholdepositum()) {
        $beboere_med_depositum[] = $beboer;
    }
}
//var_dump($krysseliste);
//var_dump($denne_vakta);
?>
<script>
    $(document).ready(function () {
        $('#tabellen').DataTable({
            "paging": false,
            "searching": false
        });

    });
</script>
<style>
    table.tableSection {
        display: table;
        width: 100%;
    }

    table.tableSection thead,
    table.tableSection tbody {
        width: 100%;
    }

    table.tableSection thead {
        overflow-y: scroll;
        display: table;
        table-layout: fixed;
        width: calc(100% - 17px); /* assuming scrollbar width as 16px */
    }

    table.tableSection tbody {
        overflow: auto;
        height: 600px;
        display: block;
    }

    table.tableSection tr {
        width: 100%;
        text-align: left;
        display: table;
        table-layout: fixed;
    }
</style>
<link rel="stylesheet" type="text/css" href="css/dataTables.css"/>
<script type="text/javascript" src="js/dataTables.js"></script>
<div class="container">
    <h1>Journal » Krysseliste</h1>
    <hr>
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

                <?php } /*
                  <th style="width: 15%;" data-sortable="true">Øl</th>
                  <th style="width: 15%;" data-sortable="true">Cider</th>
                  <th style="width: 15%;" data-sortable="true">Carlsberg</th>
                  <th style="width: 15%;" data-sortable="true">Rikdom</th>
                  <th style="width: 15%;" data-sortable="true">Pant</th>*/ ?>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($beboere_med_depositum as $beboer) { ?>
                <tr>
                    <td style="width: 25%;"><a
                            href="?a=journal/kryssing/<?php echo $beboer->getId(); ?>"><?php echo $beboer->getFulltNavn(); ?></a>
                    </td>
                    <?php foreach ($drikke as $drikken) {
                    if ($denne_vakta->drukketDenneVakta($drikken->getId()) || $drikken->getAktiv()) { ?>
                        <td style="width: 15%;"><?php echo $krysseliste[$beboer->getId()][$drikken->getNavn()]; ?></td>
                    <?php } } /*
                      <td style="width: 15%;"><?php echo $krysseliste[$beboer->getId()]['Øl'];?></td>
                      <td style="width: 15%;"><?php echo $krysseliste[$beboer->getId()]['Cider'];?></td>
                      <td style="width: 15%;"><?php echo $krysseliste[$beboer->getId()]['Carlsberg'];?></td>
                      <td style="width: 15%;"><?php echo $krysseliste[$beboer->getId()]['Rikdom'];?></td>
                      <td style="width: 15%;"><?php echo $krysseliste[$beboer->getId()]['Pant'];?></td> */
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

require_once('bunn.php');

?>
