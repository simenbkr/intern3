<?php

require_once('topp.php');

?>

<div class="col-md-12">
	<h1>Vakt &raquo; Vaktbytte</h1>
	<p>[ <a href="?a=vakt">Vaktliste</a> ] [ Vaktbytte ]</p>
</div>
<?php

foreach (range(1, 4) as $vakttype) {
	?><div class="col-md-3 col-sm-6 col-sx-12">
	<table class="table table-bordered">
		<tr>
			<th><?php echo $vakttype; ?>.&nbsp;vakt</th>
		</tr>
<?php
	foreach ($vaktbytteListe[$vakttype] as $vaktbytte) {
		?>		<tr>
			<td><input type="button" class="pull-right" value="Bytt"><?php
		echo '<strong>' . date('l d/m', strtotime($vaktbytte->getVakt()->getDato())) . '</strong>';
		echo '<br>';
		echo $vaktbytte->getVakt()->getBruker()->getPerson()->getFulltNavn();
		?></td>
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
