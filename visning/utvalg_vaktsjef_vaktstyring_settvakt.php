<div>
  <p> </p>
		<table class="table table-bordered">
			<tr>
				<th>Velg vakt</th>
			</tr>
	<?php
  foreach (intern3\VaktListe::medBrukerId($beboer->getBrukerId()) as $vakt) {
		$tid = strtotime($vakt->getDato());
		$tekst = $vakt->getVakttype() . '. vakt ' . strftime('%A %d/%m', $tid);
		?>		<tr>
	<?php
	if ($vakt->erFerdig()) {
		if (!$visFerdig) {
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
</div>
