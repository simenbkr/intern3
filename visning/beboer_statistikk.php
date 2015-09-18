<?php

require_once('topp.php');

?>

<div class="col-md-12">
	<h1>Statistikk</h1>
	<p>[ <a href="?a=beboer">Beboerliste</a> ] [ Statistikk ]</p>
<?php

foreach ($histogram as $navn => $hist) {
	?><h2><?php echo $navn; ?></h2>

<table class="histogram">
	<tr>
<?php
	$max = max($hist);
	foreach ($hist as $verdi) {
		echo '		<td>
			<div style="height: ' . (($max - $verdi) * 10) . 'px;">' . ($verdi > 2 ? ' ' : $verdi) . '</div>
			<div style="height: ' . ($verdi * 10) . 'px;">' . ($verdi > 2 ? $verdi : ' ') . '</div>
		</td>' . PHP_EOL;
	}
?>
	</tr>
	<tr>
<?php
	$snitt = 0;
	foreach ($hist as $nokkel => $verdi) {
		if (is_numeric($nokkel)) {
			$snitt += $nokkel * $verdi;
		}
		echo '		<td>' . $nokkel . '</td>' . PHP_EOL;
	}
?>
	</tr>
</table>

<?php
	if ($snitt > 0) {
		$snitt /= array_sum($hist);
		echo '	<p>Gjennomsnitt: ' . number_format($snitt, 2, ',', '') . '</p>' . PHP_EOL;
	}
}

?>
</div>

<?php

require_once('bunn.php');

?>
