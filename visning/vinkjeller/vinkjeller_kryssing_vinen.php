<?php
require_once('topp_vinkjeller.php');
require_once(__DIR__ . '/../static/topp.php');
/* @var \intern3\Vin $vinen */
?>
<link rel="stylesheet" type="text/css" href="css/dataTables.css"/>
<script type="text/javascript" src="js/dataTables.js"></script>
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

    body {
      background-color: #444341;
      color: #FFF;
    }
    table {
      color: #000;
    }
</style>
<div class="container">

    <h1>Vinkjeller » Kryss <?php echo $vinen->getNavn(); ?></h1>
    <hr>
    <div class="col-lg-12">
      <div class="col-lg-2" style="text-align: center">
        <?php if($vinen->getBilde() != null && $vinen->getBilde() != ""){ ?>
        <img height="300px" src="vinbilder/<?php echo $vinen->getBilde(); ?>">
        <?php } else { ?>
        <img src="vinbilder/default.png" style="max-height: 300px">
        <?php } ?>
      </div>
      <div class="col-lg-10">
        <div class="col-lg-10">
          <h1><?php echo $vinen->getNavn(); ?></h1>
        </div>
      </div>
      <div class="col-lg-10">
        <?php if (!$vinen->getLand() == 'udefinert' && $vinen->getLand() == ''){?>
        <div class="col-lg-10" style="margin-top: 20px;">
          <h2><?php echo $vinen->getLand(); ?></h2>
        </div>
      <?php }?>
      </div>
      <div class="col-lg-10">
        <div class="col-lg-2" style="margin-top: 20px;">
          <h2><?php echo round($vinen->getPris() * $vinen->getAvanse(), 2); ?> kr</h2>
        </div>
      </div>
      <?php if ($vinen->getBeskrivelse()){?>
      <div class="col-lg-10">
        <div class="col-lg-10" style="margin-top: 20px;">
          <h2><?php echo $vinen->getBeskrivelse(); ?></h2>
        </div>
      </div>
      <?php }?>
      <div class="col-lg-10">
        <div class="col-lg-10" style="margin-top: 20px;">
          <h2>Antall: <?php echo round($vinen->getAntall()); ?></h2>
        </div>
      </div>

    </div>
    <div class="col-lg-12">
      <hr>
      <button class="btn btn-primary btn-block" onclick="videre()">Velg</button>
      <br/>
    </div>

    <div class="col-lg-12">
      <p>Står du ikke på lista? Sjekk at du har satt pinkode!</p>
    </div>
      <table id="tabellen" data-togle="table" class="table table-bordered table-responsive tableSection">
          <thead>
          <tr>
              <td>Navn</td>
          </tr>
          </thead>

          <tbody>
          <?php

          foreach ($beboerListe as $beboer) {
              /* @var intern3\Beboer $beboer */

              if($beboer->getPrefs() == null ||
                 !$beboer->getPrefs()->harVinPin() ||
                 strlen($beboer->getPrefs()->getVinPinkode() < 1) ){

                  continue;
              }
              ?>

              <tr id="<?php echo $beboer->getId(); ?>" onclick="select(<?php echo $beboer->getId(); ?>)">
                  <td><?php echo $beboer->getFulltNavn(); ?></td>
              </tr>
              <?php
          }
          ?>
          </tbody>
      </table>


</div>

<script>

    $(document).ready(function () {
        $('#tabellen').DataTable({
            "paging": false,
            "searching": false
        });

    });

    var ids = [];
    function select(id) {

        var x = document.getElementById(id);
        if (ids.indexOf(id) == -1) {
            x.style.backgroundColor = "green";
            ids.push(id);
        }
        else {
            var indeks = ids.indexOf(id);
            ids.splice(indeks, 1);
            x.style.backgroundColor = "white";
        }
    }

    function videre() {
        var idString = ids.join('/');
        $.ajax({
            type: 'POST',
            url: '?a=vinkjeller/kryssing/<?php echo $vinen->getId();?>/' + idString,
            method: 'GET',
            success: function (html) {
                window.location.href = '?a=vinkjeller/kryssing/<?php echo $vinen->getId();?>/' + idString;
            },
            error: function (req, stat, err) {
                alert(err);
            }
        });
    }
</script>

<?php
require_once(__DIR__ . '/../static/bunn.php');
?>
