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
      <!-- <div class="container-fluid"> -->

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
          <table id="tabellen" class="table table-bordered table-responsive tableSection" data-toggle="table">
              <thead>
              <tr>
                  <th style="width: 25%;" data-sortable="true">Navn</th>
                  <th style="width: 15%;" data-sortable="true">Øl</th>
                  <th style="width: 15%;" data-sortable="true">Cider</th>
                  <th style="width: 15%;" data-sortable="true">Carlsberg</th>
                  <th style="width: 15%;" data-sortable="true">Rikdom</th>
                  <th style="width: 15%;" data-sortable="true">Pant</th>
              </tr>
              </thead>
              <tbody>
              <?php
              foreach($beboere_med_depositum as $beboer){ ?>
                  <tr>
                      <td style="width: 25%;"><a href="?a=journal/kryssing/<?php echo $beboer->getId(); ?>"><?php echo $beboer->getFulltNavn(); ?></a></td>
                      <td style="width: 15%;"><?php echo $krysseliste[$beboer->getId()]['Øl'];?></td>
                      <td style="width: 15%;"><?php echo $krysseliste[$beboer->getId()]['Cider'];?></td>
                      <td style="width: 15%;"><?php echo $krysseliste[$beboer->getId()]['Carlsberg'];?></td>
                      <td style="width: 15%;"><?php echo $krysseliste[$beboer->getId()]['Rikdom'];?></td>
                      <td style="width: 15%;"><?php echo $krysseliste[$beboer->getId()]['Pant'];?></td>
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
