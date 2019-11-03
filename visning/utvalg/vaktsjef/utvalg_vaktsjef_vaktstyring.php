<?php

require_once(__DIR__ . '/../topp_utvalg.php');


?>

<script>
  // Legger til mulighet for å endre klokkeslett for vaktslipp
  $(function () {
      $('#datoen3').datepicker({
          dateFormat: 'yy-mm-dd',
          onSelect: function (datetext) {
              var d = new Date(); // for now
              var h = d.getHours();
              h = (h < 10) ? ("0" + h) : h;

              var m = d.getMinutes();
              m = (m < 10) ? ("0" + m) : m;

              var s = d.getSeconds();
              s = (s < 10) ? ("0" + s) : s;

              datetext = datetext + " " + h + ":" + m;
              $('#datoen3').val(datetext);
          },
      });
  });

function modal() {
  var modalId = $(this).attr('data-target');
  var vakttype = $(this).attr('data-type');
  var unix = $(this).attr('data-unix');
  var vaktId_1 = $(this).attr('data-id');
  var dataString = 'modalId=' + modalId + '&vakttype=' + vakttype + '&unix=' + unix + '&vaktId_1=' + vaktId_1;
  $.ajax({
    cache: false,
    type: 'POST',
    url: '?a=utvalg/vaktsjef/vaktstyring_modal',
    data: dataString,
    success: function(data) {
      $('#get-' + modalId).html(data);
      $('#' + modalId).modal('show');
      $('#' + modalId).on('hidden.bs.modal', function() {
        $('#get-' + modalId).html(' ');
      });
    }
  });
}
function lagVakt(modalId) {
  $.ajax({
    cache: false,
    type: 'POST',
    url: '?a=utvalg/vaktsjef/vaktstyring_lagvakt',
    data: 'modalId=' + modalId,
    success: function(data) {
      location.reload();
    }
  });
}

function setVar(){
    $.ajax({
    cache: false,
    type: 'POST',
    url: '?a=utvalg/vaktsjef/setvar',
    success: function(data) {
      location.reload();
    }
  });
}

function setHost(){
    $.ajax({
    cache: false,
    type: 'POST',
    url: '?a=utvalg/vaktsjef/sethost',
    success: function(data) {
      location.reload();
    }
  });
}

</script>

<div class="col-md-12">
	<h1>Utvalget &raquo; Vaktsjef &raquo; Vaktstyring</h1>
	<hr>

<div class="btn-group" role="group">
  <div class="btn-group" role="group">
    <button id="btnGroupDrop1" type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      Semester <span class="caret"></span>
    </button>
    <ul class="dropdown-menu">
      <li><a href="#" onclick="setVar()">Vår</a></li>
      <li><a href="#" onclick="setHost()">Høst</a></li>
    </ul>
  </div>
  <button data-toggle="modal" class="btn btn-primary" data-target="#modal-leggutvakter">Vaktslipp</button>
</div>

<div class="modal fade" aria-hidden="true" id="modal-leggutvakter" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Velg vakter som skal legges ut</h4>
            </div>
            <div class="modal-body">
                <form method="post" action="?a=utvalg/vaktsjef/publiser">
                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                  <label class="btn btn-secondary active">
                    <input type="radio" name="options" value="1" autocomplete="off" checked> 1. Vakt
                  </label>
                  <label class="btn btn-secondary">
                    <input type="radio" name="options" value="2" autocomplete="off"> 2. Vakt
                  </label>
                  <label class="btn btn-secondary">
                    <input type="radio" name="options" value="3" autocomplete="off"> 3. Vakt
                  </label>
                  <label class="btn btn-secondary">
                    <input type="radio" name="options" value="4" autocomplete="off"> 4. Vakt
                  </label>
                </div>
                    <div class="container">
                      <div class="row">
                        <div class="col-sm-3">
                          <p><input class="datepicker form-control" name="start" id="datoen0" placeholder="Start" type="text" required/></p>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-3">
                          <p><input class="datepicker form-control" name="slutt" id="datoen2" placeholder="Slutt" type="text" required/></p>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-3">
                          <p><input class="form-control" name="slipp" id="datoen3" placeholder="Slippdato" type="text" required/></p>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-3">
                          <input type="submit" class="btn btn-md btn-primary" value="Send inn" name="tabell">
                        </div>
                      </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Lukk</button>
            </div>
        </div>
    </div>
</div>

	<hr>

<?php
/*
 * Vet ikke hvem som gjorde detta statisk, men sett deg på en svær dildo. Takk.
if (date('m') > 6) {
	$ukeStart = strtotime('1 September');
	$ukeSlutt = strtotime('1 January + 1 year');
}
else {
	$ukeStart = strtotime('1 January');
	$ukeSlutt = strtotime('1 July');
	if (date('W', $ukeStart) == 53) {
		$ukeStart = strtotime('next week', $ukeStart);
	}
}
$ukeStart = strtotime('last week', $ukeStart);
*/

if ($_SESSION['semester'] == "var"){
    $ukeStart = strtotime('1 January');
	$ukeSlutt = strtotime('1 July');
	if (date('W', $ukeStart) == 53) {
		$ukeStart = strtotime('next week', $ukeStart);
	}
} else {
    $ukeStart = strtotime('5 August');
	$ukeSlutt = strtotime('1 January + 1 year');
}
$ukeStart = strtotime('last week', $ukeStart);


foreach (range(date('W', $ukeStart), date('W', $ukeSlutt)) as $uke){
	$ukeStart = strtotime('+1 week', $ukeStart);
?>
	<table class="table-bordered table">
		<tr>
			<th style="width:5.5%;"><span class="hidden-sm hidden-xs">Uke&nbsp;</span><?php echo intval(date('W', $ukeStart)); ?></th>
			<th style="width:13.5%;">M<span class="hidden-xs">an<span class="hidden-sm">dag</span></span>&nbsp;<?php echo date('d/m',  $ukeStart);?></th>
			<th style="width:13.5%;">T<span class="hidden-xs">ir<span class="hidden-sm">sdag</span></span>&nbsp;<?php echo date('d/m', strtotime('+1 day', $ukeStart));?></th>
			<th style="width:13.5%;">O<span class="hidden-xs">ns<span class="hidden-sm">dag</span></span>&nbsp;<?php echo date('d/m', strtotime('+2 day', $ukeStart));?></th>
			<th style="width:13.5%;">T<span class="hidden-xs">or<span class="hidden-sm">sdag</span></span>&nbsp;<?php echo date('d/m', strtotime('+3 day', $ukeStart));?></th>
			<th style="width:13.5%;">F<span class="hidden-xs">re<span class="hidden-sm">dag</span></span>&nbsp;<?php echo date('d/m', strtotime('+4 day', $ukeStart));?></th>
			<th style="width:13.5%;">L<span class="hidden-xs">ør<span class="hidden-sm">dag</span></span>&nbsp;<?php echo date('d/m', strtotime('+5 day', $ukeStart));?></th>
			<th style="width:13.5%;">S<span class="hidden-xs">øn<span class="hidden-sm">dag</span></span>&nbsp;<?php echo date('d/m', strtotime('+6 day', $ukeStart));?></th>
		</tr>
<?php
	foreach (range(1, 4) as $vakttype){
?>
		<tr>
			<td><?php echo $vakttype; ?>.<span class="hidden-sm hidden-xs">&nbsp;vakt</span><br>&nbsp;</td>
<?php
		foreach (range(0, 6) as $ukedag) {
      $unix = strtotime('+' . $ukedag . ' day', $ukeStart);
      $dato = date('Y-m-d', $unix);
			$vakt = intern3\Vakt::medDatoVakttype($dato, $vakttype);
      $modalId = 'modal-' . $dato . '-' . $vakttype;
			if ($vakt == null) {
				echo '			<td style="text-align: center;"><input type="button" onclick="lagVakt(\'' . $modalId . '\')" class="btn btn-sm btn-info" value="Legg til vakt">' . PHP_EOL;
			}
			else {if ($vakt->erLedig()) {
				echo '			<td style="text-align: center;"><input type="button" onclick="modal.call(this)" class="btn btn-sm btn-info" value="Endre" data-target="' . $modalId . '" data-type="' . $vakttype . '" data-unix="' . $unix . '" data-id="' . $vakt->getId() . '">' . PHP_EOL;
			}
      else {if ($vakt->getBruker() == null) {
        echo '			<td style="text-align: center;"><input type="button" onclick="modal.call(this)" class="btn btn-sm btn-warning" value="Endre" data-target="' . $modalId . '" data-type="' . $vakttype . '" data-unix="' . $unix . '" data-id="' . $vakt->getId() . '">' . PHP_EOL;
      }
      else {
  			$bruker = $vakt->getBruker();
        if ($vakt->erDobbelvakt()) {
          echo '			<td class="celle_blaa">';
        }
        else {if ($vakt->erStraffevakt()) {
          echo '			<td class="celle_oransje">';
        }
        else {if ($vakt->vilBytte()) {
          echo '			<td class="celle_lyseblaa">';
        }
        else {
          echo '			<td>';
        }}}
  			if ($bruker == null || $bruker->getPerson() == null) {
  				echo ' ';
  			}
  			else {
          echo '			 <a href="JavaScript:void(0);" onclick="modal.call(this)" data-target="' . $modalId . '" data-type="' . $vakttype . '" data-unix="' . $unix . '" data-id="' . $vakt->getId() . '">' . PHP_EOL;
          if(!$bruker->getPerson()->erBeboer()){
              echo "(UTFLYTTET) ";
          }
          echo $bruker->getPerson()->getFulltNavn();
          echo '</a>' . PHP_EOL;
  			}
      }}}
      echo '			 <div id="get-' . $modalId . '">' . PHP_EOL;
?>
      </div>
    </td>
<?php } ?>
  </tr>
<?php
	}
?>
	</table>
<?php
}

?>
</div>
<?php

require_once(__DIR__ . '/../../static/bunn.php');

?>
