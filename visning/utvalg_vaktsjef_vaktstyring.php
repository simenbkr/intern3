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
      <!-- Modal for vakter -->
      <div class="modal fade" tabindex="-1" id="<?php echo $modalId; ?>" role="dialog">
        <div class="modal-dialog">
          <div class="modal-content panel-primary">
            <div class="modal-header panel-heading">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title" align="center"><?php echo $vakttype . '. vakt ' . strftime('%A %d/%m', $unix); ?></h4>
            </div>
            <div class="modal-body" align="center">
              <p> </p>
              <input type="button" class="btn btn-sm btn-primary" value="Bytt vakt" data-toggle="modal" data-target="#<?php echo $modalId; ?>-byttvakt">
              <input type="button" class="btn btn-sm btn-primary" value="Dobbelvakt" data-target="#<?php echo $modalId; ?>-dobbelvakt">
              <input type="button" class="btn btn-sm btn-warning" value="Straffevakt" data-target="#<?php echo $modalId; ?>-straffevakt">
              <input type="button" class="btn btn-sm btn-danger" value="Slett vakt" data-toggle="modal" data-target="#<?php echo $modalId; ?>-slettvakt">
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Lukk</button>
            </div>
          </div>
        </div>
      </div>
      <!-- Modal for ledige vakter -->
      <div class="modal fade" tabindex="-1" id="<?php echo $modalId; ?>-ledig" role="dialog">
        <div class="modal-dialog modal-sm">
          <div class="modal-content panel-primary">
            <div class="modal-header panel-heading">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title" align="center"><?php echo $vakttype . '. vakt ' . strftime('%A %d/%m', $unix); ?></h4>
            </div>
            <div class="modal-body" align="center">
              <!-- <div class="alert alert-info fade in" id="lagret" style="display: none;">Lagret!<button type="button" class="close" onclick="hideLagret()">&times;</button>
              </div> -->
              <p>Velg hvem som skal ha vakten</p>
              <select onchange="velgBeboer(this.value)">
                <option value="0">- velg -</option>

                <?php
                foreach ($beboerListe as $beboer) { // Denne velger kun de som har vakt
                ?>

                <option value="<?php echo $beboer->getId(); ?>">
                <?php echo $beboer->getFulltNavn(); ?>
                </option>

                <?php
                }
                ?>
              </select>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Lukk</button>
              <button type="button" class="btn btn-sm btn-primary" id="lagre">Lagre</button> <!-- TODO må fikses! -->
            </div>
          </div>
        </div>
      </div>
      <!-- Modal for å slette vakter -->
      <div class="modal fade" tabindex="-1" id="<?php echo $modalId; ?>-slettvakt" role="dialog">
        <div class="modal-dialog modal-sm">
          <div class="modal-content panel-primary">
            <div class="modal-header panel-heading">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title" align="center"><?php echo $vakttype . '. vakt ' . strftime('%A %d/%m', $unix); ?></h4>
            </div>
            <div class="modal-body" align="center">
              <p>Er du sikker på at du vil slette denne vakten?</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Nei</button>
              <button type="button" class="btn btn-sm btn-primary" data-target="DOSOMETHING!" data-dismiss="modal">Ja</button> <!-- TODO må fikses! (DOSOMETHING!) -->
            </div>
          </div>
        </div>
      </div>
      <!-- Modal for å bytte vakt -->
      <div class="modal fade" tabindex="-1" id="<?php echo $modalId; ?>-byttvakt" role="dialog">
        <div class="modal-dialog modal-sm">
          <div class="modal-content panel-primary">
            <div class="modal-header panel-heading">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title" align="center"><?php echo $vakttype . '. vakt ' . strftime('%A %d/%m', $unix); ?></h4>
            </div>
            <div class="modal-body" align="center">
              <p>Velg hvem som skal ha vakten</p>
              <select onchange="velgBeboer(this.value)">
                <option value="0">- velg -</option>

                <?php
                foreach ($beboerListe as $beboer) { // Denne velger kun de som har vakt
                ?>

                <option value="<?php echo $beboer->getId(); ?>">
                <?php echo $beboer->getFulltNavn(); ?>
                </option>

                <?php
                }
                ?>
              </select>
              <div id="settvakt">
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Lukk</button>
            </div>
          </div>
        </div>
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
