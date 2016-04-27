<?php

require_once('topp_utvalg.php');

?>

<div class="col-md-12">
	<h1>Utvalget &raquo; Vaktsjef &raquo; Vaktstyring</h1>

<?php

if (date('m') > 6) {
	$ukeStart = strtotime('1 July');
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
				echo '			<td class="celle_graa">Torild Fivë</td>' . PHP_EOL; //Må fikses!!
				continue;
			}
			else if ($vakt == null) {
				echo '			<td style="text-align: center;"><input type="button" class="btn btn-sm btn-info" value="Endre" data-toggle="modal" data-target="#' . $modalId . '"></td>' . PHP_EOL;
			}
			else if ($vakt->erLedig()) {
				echo '			<td style="text-align: center;"><input type="button" class="btn btn-sm btn-info" value="Endre" data-toggle="modal" data-target="#' . $modalId . '"></td>' . PHP_EOL;
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
      <div class="modal fade" id="<?php echo $modalId; ?>" role="dialog">
        <div class="modal-dialog modal-sm">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title"><?php echo $vakttype . '. vakt ' . strftime('%A %d/%m', $unix); ?></h4>
            </div>
            <div class="modal-body">
              <p>Bytte vakt</p>
              <p>Gi vakt</p>
              <p>Slett vakt</p>
              <p>Sett vakt som ledig</p>
              <p>Dobbelvakt</p>
              <p>Straffevakt</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Lukk</button>
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
