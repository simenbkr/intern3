<?php

require_once('topp.php');

?>
<div class="col-md-12">
	<h1>Vakt &raquo; Vaktliste</h1>
	<p>[ Vaktliste ] [ <a href="?a=vakt/bytte">Vaktbytte</a> ]</p>
	<p><span class="tekst_dinevakter"><?php
if ($cd->getAktivBruker()->getPerson()->harVakt()) {
	echo 'Dine vakter';
}
else {
	echo 'Du skal ikke sitte vakter';
}
?></span>, <span class="tekst_ledigevakter">Ønsker å bytte</span>
<?php

$ukeStart = strtotime('last sunday - 6 days, midnight');

foreach (range($denneUka, $denneUka > 26 ? date('W', mktime(0, 0, 0, 12, 31, date('Y'))) : 26) as $uke){
	$ukeStart = strtotime('+1 week', $ukeStart);
?>
	<table class="table-bordered table">
		<tr>
			<th style="width:5.5%;"><span class="hidden-sm hidden-xs">Uke&nbsp;</span><?php echo $uke; ?></th>
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
			$vakt = intern3\Vakt::medDatoVakttype(date('Y-m-d', strtotime('+' . $ukedag . ' day', $ukeStart)), $vakttype);
      
			if ($vakt == null && $vakttype==2 && $ukedag>=0 && $ukedag<=4) {
				echo '			<td class="celle_graa">Torild Fivë</td>' . PHP_EOL;
				continue;
			}
			if ($vakt == null) {
				echo '			<td> </td>' . PHP_EOL;
				continue;
			}
			if ($vakt->erLedig()) {
				// echo '			<td style="text-align: center;"><input type="button" class="btn btn-info" value="Ledig"></td>' . PHP_EOL;
        echo '			<td> </td>' . PHP_EOL;
				continue;
			}
			$bruker = $vakt->getBruker();
			echo '			<td';
			if ($vakt->erLedig() || $vakt->vilBytte()) {
				echo ' class="tekst_ledigevakter"';
			}
			else if ($bruker <> null && $bruker->getId() == $cd->getAktivBruker()->getId()) {
				echo ' class="tekst_dinevakter"';
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
