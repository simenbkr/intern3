	<table class="histogram">
		<tr>
<?php
	$maks = max($hist);
	foreach ($hist as $verdi) {
		$runda = str_replace('.', ',', round($verdi, 1));
		echo '			<td>
				<div style="height: ' . floor(($maks - $verdi) * 10) . 'px;">' . ($verdi > 2 ? ' ' : $runda) . '</div>
				<div style="height: ' . ceil($verdi * 10) . 'px;">' . ($verdi > 2 ? $runda : ' ') . '</div>
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
		echo '			<td>' . $nokkel . '</td>' . PHP_EOL;
	}
?>
		</tr>
	</table>

<?php
	if ($snitt > 0) {
		$snitt /= array_sum($hist);
		echo '	<p>Gjennomsnitt: ' . number_format($snitt, 2, ',', '') . '</p>' . PHP_EOL;
	}
	?>
