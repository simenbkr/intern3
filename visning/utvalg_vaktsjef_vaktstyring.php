<?php

require_once('topp_utvalg.php');

?>

<script>
function velgBeboer(id) {
  if (id == 0) {
    $('#').find('.modal-body').append(id);
    $('#modal').find('.modal-body').append(id);
    $('#settvakt').html(' ');
  }
  else {
    $.get('?a=utvalg/vaktsjef/vaktstyring_settvakt/'+id, function(data) {
      $('#').find('.modal-body').append(id);
      $('#modal').find('.modal-body').append(id);
      $('#settvakt').html(data);
    });
  }
}
// $(function modalId() {
//   $('.modal').on('shown.bs.modal', function() {
//     var modalId = $(this).attr('id');
//     // $('#'+modalId).find('.modal-body').prepend('<p>' + modalId + '</p>');
//   });
// });
</script>

<div class="col-md-12">
	<h1>Utvalget &raquo; Vaktsjef &raquo; Vaktstyring</h1>

<?php

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

			if ($vakt == null && $vakttype==2 && $ukedag>=0 && $ukedag<=4) {
        echo '			<td class="celle_graa">';
        echo '			<a href="JavaScript:void(0);" data-toggle="modal" data-target="#' . $modalId . '">' . PHP_EOL; // TODO funker ikke!
				echo $torild->getFulltNavn();
        echo '</a>' . PHP_EOL;
        continue; // TODO må fjernes
			}
			else if ($vakt == null) {
				echo '			<td style="text-align: center;"><input type="button" class="btn btn-sm btn-info" value="Endre" data-toggle="modal" data-target="#' . $modalId . '-ledig"></td>' . PHP_EOL;
			}
			else if ($vakt->erLedig()) {
				echo '			<td style="text-align: center;"><input type="button" class="btn btn-sm btn-info" value="Endre" data-toggle="modal" data-target="#' . $modalId . '-ledig"></td>' . PHP_EOL;
			}
      else {
  			$bruker = $vakt->getBruker();
  			echo '			<td>';
  			if ($bruker == null) {
  				echo ' ';
  			}
  			else {
          echo '			 <a href="JavaScript:void(0);" data-toggle="modal" data-target="#' . $modalId . '">' . PHP_EOL;
          echo $bruker->getPerson()->getFulltNavn();
          echo '</a>' . PHP_EOL;
  			}
      }
		  ?>
      <div id="modal">
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
