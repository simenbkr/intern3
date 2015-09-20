<?php

require_once('topp.php');

?>
<div class="col-md-12">
	<h1>Vaktliste</h1>
	<p><span style="color: #090;">Du skal ikke sitte vakter</span>, evt <span style="color: #090;">Dine vakter</span>, <span style="color: #009;">Ledige vakter</span>
<?php

$ukeStart = strtotime('last sunday - 6 days, midnight');

foreach (range($denneUka, $denneUka > 26 ? 52 : 26) as $uke){
	$ukeStart = strtotime('+1 week', $ukeStart);
?>
	<table class="table-bordered table">
		<tr>
			<th><span class="hidden-xs">U<span class="hidden-sm">ke</span>&nbsp;</span><?php echo $uke; ?></th>
			<th>M<span class="hidden-xs">an<span class="hidden-sm">dag</span></span>&nbsp;<?php echo date('d/m',  $ukeStart);?></th>
			<th>T<span class="hidden-xs">ir<span class="hidden-sm">sdag</span></span>&nbsp;<?php echo date('d/m', strtotime('+1 day', $ukeStart));?></th>
			<th>O<span class="hidden-xs">ns<span class="hidden-sm">dag</span></span>&nbsp;<?php echo date('d/m', strtotime('+2 day', $ukeStart));?></th>
			<th>T<span class="hidden-xs">or<span class="hidden-sm">sdag</span></span>&nbsp;<?php echo date('d/m', strtotime('+3 day', $ukeStart));?></th>
			<th>F<span class="hidden-xs">re<span class="hidden-sm">dag</span></span>&nbsp;<?php echo date('d/m', strtotime('+4 day', $ukeStart));?></th>
			<th>L<span class="hidden-xs">ør<span class="hidden-sm">dag</span></span>&nbsp;<?php echo date('d/m', strtotime('+5 day', $ukeStart));?></th>
			<th>S<span class="hidden-xs">øn<span class="hidden-sm">dag</span></span>&nbsp;<?php echo date('d/m', strtotime('+6 day', $ukeStart));?></th>
		</tr>
<?php
	foreach (range(1, 4) as $vakttype){
		?>
		<tr>
			<td><?php echo $vakttype; ?>.<span class="hidden-xs"> vakt</span></td>
<?php
		foreach (range(0, 6) as $ukeDag) {
			$vakt = intern3\Vakt::medDatoVakttype(date('Y-m-d', strtotime('+' . $ukeDag . ' day', $ukeStart)), $vakttype);
			if ($vakt == null) {
				echo '			<td> </td>' . PHP_EOL;
				continue;
			}
			$bruker = $vakt->getBruker();
			echo '			<td';
			if ($vakt <> null && $vakt->erLedig()) {
				echo ' style="color: #009;"';
			}
			else if ($bruker <> null && $bruker->getId() == $cd->getAktivBruker()->getId()) {
				echo ' style="color: #090;"';
			}
			echo '>';
			if ($bruker == null) {
				echo ' ';
			}
			else {
				echo $bruker->getPerson()->getFulltNavn();
			}
			echo '</td>' . PHP_EOL;
		}
		?>
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
