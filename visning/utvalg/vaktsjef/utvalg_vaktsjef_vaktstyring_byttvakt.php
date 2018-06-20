<?php
if(isset($_POST['vaktId_1'])) {
  $vaktId_1 = $_POST['vaktId_1'];
}
if(isset($_POST['brukerId'])) {
  $vaktId_1 = $_POST['brukerId'];
}
?>
<script>
function bytt(vaktId_1, vaktId_2) {
  $.ajax({
    cache: false,
    type: 'POST',
    url: '?a=utvalg/vaktsjef/vaktstyring_byttvakt',
    data: 'vaktId_1=' + vaktId_1 + '&vaktId_2=' + vaktId_2,
    success: function(data) {
      location.reload();
    }
  });
}
</script>
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
		if (isset($visFerdig)) {
			?>
					<td class="celle_graa"><?php echo $tekst; ?></td>
		<?php
		}
	}
	else {
		?>			<td><?php echo $tekst; ?>
					<input class="btn btn-sm btn-warning pull-right" onclick="bytt(<?php echo $vaktId_1; ?>, <?php echo $vakt->getId(); ?>)" type="button" value="Bytt">
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
