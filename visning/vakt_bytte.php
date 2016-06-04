<?php

require_once('topp.php');

?>

<div class="col-md-12">
	<h1>Vakt &raquo; Vaktbytte</h1>
	<p>[ <a href="<?php echo $cd->getBase(); ?>vakt">Vaktliste</a> ] [ Vaktbytte ]</p>
</div>
<div class="col-md-3 col-sm-6 col-sx-12">
<?php
function visDineVakter($visFerdig = true) {
	global $cd;
	?>
		<table class="table table-bordered">
			<tr>
				<th>Dine vakter</th>
			</tr>
	<?php
	foreach (intern3\VaktListe::medBrukerId($cd->getAktivBruker()->getId()) as $vakt) {
		$tid = strtotime($vakt->getDato());
		$tekst = $vakt->getVakttype() . '. vakt ' . strftime('%A %d/%m', $tid);
		?>		<tr>
	<?php
	if ($vakt->erFerdig()) {
		if ($visFerdig) {
			?>
					<td class="celle_graa"><?php echo $tekst; ?></td>
		<?php
		}
	}
	else {
		?>			<td><?php echo $tekst; ?>
					<input class="btn btn-sm btn-warning pull-right" type="button" value="Bytt">
				</td>
	<?php
	}
	?>
			</tr>
	<?php
		}
	?>
		</table>
<?php
}
visDineVakter();
?>
</div>
<div class="col-md-12"> </div>
<?php

foreach (range(1, 4) as $vakttype) {
	?><div class="col-md-3 col-sm-6 col-sx-12">
	<table class="table table-bordered">
		<tr>
			<th><?php echo $vakttype; ?>.&nbsp;vakt</th>
		</tr>
<?php
	foreach ($vaktbytteListe[$vakttype] as $vaktbytte) {
		$bruker = $vaktbytte->getVakt()->getBruker();
		if ($bruker == null) {
			continue;
		}
		$modalId = 'modal-' . date('m-d', strtotime($vaktbytte->getVakt()->getDato())) . '-' . $vaktbytte->getVakt()->getVakttype();
		?>		<tr>
			<td>
<?php
		if ($vaktbytte->getVakt()->getBrukerId() != $cd->getAktivBruker()->getId()) {
			echo '				<input type="button" class="btn btn-sm btn-info pull-right" value="Bytt" data-toggle="modal" data-target="#' . $modalId . '">' . PHP_EOL;
		}
		echo '				<strong>' . ucfirst(strftime('%A %d/%m', strtotime($vaktbytte->getVakt()->getDato()))) . '</strong>' . PHP_EOL;
		echo '				<br>' . PHP_EOL;
		echo $bruker->getPerson()->getFulltNavn();
?>
				<div class="modal fade" id="<?php echo $modalId; ?>" role="dialog">
					<div class="modal-dialog modal-sm">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
								<h4 class="modal-title"><?php echo $vaktbytte->getVakt()->getVakttype() . '. vakt ' . strftime('%A %d/%m', strtotime($vaktbytte->getVakt()->getDato())); ?></h4>
							</div>
							<div class="modal-body">
								<p>Hvilken vakt vil du foreslå å bytte?</p>
								<?php visDineVakter(false); ?>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-default" data-dismiss="modal">Lukk</button>
							</div>
						</div>
					</div>
				</div>
			</td>
		</tr><?php
	}
	?>	</table>
</div>
<?php
}

?>

<?php

require_once('bunn.php');

?>
