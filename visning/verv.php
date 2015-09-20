<?php

require_once('topp.php');

?>
<div class="col-md-12">
	<h1>Verv</h1>
	<table class="table table-bordered">
		<tr>
			<th>Åpmandsverv</th>
			<th>Åpmand/åpmend</th>
			<th>Epost</th>
		</tr>
<?php

foreach ($vervListe as $verv) {
	?>		<tr>
			<td><?php echo $verv->getNavn(); ?></td>
			<td><?php
	$i = 0;
	foreach ($verv->getApmend() as $apmand) {
		if ($i++ > 0) {
			echo ', ';
		}
		echo '<a href="?a=beboer/' . $apmand->getId() . '">' . $apmand->getFulltNavn() . '</a>';
	}
	?></td>
			<td><?php
	$epost = $verv->getEpost();
	if ($verv == null) {
		echo ' ';
	}
	else {
		echo '<a href="mailto:' . $epost . '">' . $epost . '</a>';
	}
	?></td>
		</tr><?php
}

?>
	</table>
</div>
<?php

require_once('bunn.php');

?>
