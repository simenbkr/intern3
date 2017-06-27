<?php

require_once('topp_utvalg.php');

?>

<script>
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
<div class="dropdown">
  <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">Semester
  <span class="caret"></span></button>
  <ul class="dropdown-menu">
    <li><a href="#" onclick="setVar()">Vår</a></li>
    <li><a href="#" onclick="setHost()">Høst</a></li>
  </ul>
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
    $ukeStart = strtotime('1 September');
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

			// if ($vakt == null && $vakttype==2 && $ukedag>=0 && $ukedag<=4) {
      //   echo '			<td class="celle_graa">';
      //   echo '			<a href="JavaScript:void(0);" onclick="modal.call(this)" data-target="' . $modalId . '" data-type="' . $vakttype . '" data-unix="' . $unix . '">' . PHP_EOL; // TODO funker ikke!
			// 	echo $torild->getFulltNavn();
      //   echo '</a>' . PHP_EOL;
      //   // continue; // TODO må fjernes
			// }
			if ($vakt == null) {
				echo '			<td style="text-align: center;"><input type="button" onclick="lagVakt(\'' . $modalId . '\')" class="btn btn-sm btn-info" value="Legg til vakt">' . PHP_EOL;
			}
			else if ($vakt->erLedig()) {
				echo '			<td style="text-align: center;"><input type="button" onclick="modal.call(this)" class="btn btn-sm btn-info" value="Endre" data-target="' . $modalId . '" data-type="' . $vakttype . '" data-unix="' . $unix . '" data-id="' . $vakt->getId() . '">' . PHP_EOL;
			}
      else if ($vakt->getBruker() == NULL) {
        echo '			<td style="text-align: center;"><input type="button" onclick="modal.call(this)" class="btn btn-sm btn-warning" value="Endre" data-target="' . $modalId . '" data-type="' . $vakttype . '" data-unix="' . $unix . '" data-id="' . $vakt->getId() . '">' . PHP_EOL;
      }
      else {
  			$bruker = $vakt->getBruker();
        if ($vakt->erDobbelvakt()) {
          echo '			<td class="celle_blaa">';
        }
        else if ($vakt->erStraffevakt()) {
          echo '			<td class="celle_oransje">';
        }
        else if ($vakt->vilBytte()) {
          echo '			<td class="celle_lyseblaa">';
        }
        else {
          echo '			<td>';
        }
  			if ($bruker == null || $bruker->getPerson() == null) {
  				echo ' ';
  			}
  			else {
          echo '			 <a href="JavaScript:void(0);" onclick="modal.call(this)" data-target="' . $modalId . '" data-type="' . $vakttype . '" data-unix="' . $unix . '" data-id="' . $vakt->getId() . '">' . PHP_EOL;
          echo $bruker->getPerson()->getFulltNavn();
          echo '</a>' . PHP_EOL;
  			}
      }
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

require_once('bunn.php');

?>
