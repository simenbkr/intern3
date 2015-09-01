<?php

require_once('topp.php');

?>
<h1>Vaktliste</h1>
<?php

$denneUka = date('W');
$detteAret = date('Y');

foreach (range($denneUka, $denneUka > 26 ? 52 : 26) as $uke){
?>
<table class="table-bordered table">
	<tr>
		<th><span class="hidden-xs">U<span class="hidden-sm">ke</span>&nbsp;</span><?php echo $uke; ?></th>
		<th>M<span class="hidden-xs">an<span class="hidden-sm">dag</span></span>&nbsp;<?php echo date('d/m', strtotime("+ ".($uke - $denneUka)." week"));?></th>
		<th>T<span class="hidden-xs">ir<span class="hidden-sm">sdag</span></span>&nbsp;<?php echo date('d/m', strtotime("+ ".($uke - $denneUka)." week +1 day"));?></th>
		<th>O<span class="hidden-xs">ns<span class="hidden-sm">dag</span></span>&nbsp;<?php echo date('d/m', strtotime("+ ".($uke - $denneUka)." week +2 day"));?></th>
		<th>T<span class="hidden-xs">or<span class="hidden-sm">sdag</span></span>&nbsp;<?php echo date('d/m', strtotime("+ ".($uke - $denneUka)." week +3 day"));?></th>
		<th>F<span class="hidden-xs">re<span class="hidden-sm">dag</span></span>&nbsp;<?php echo date('d/m', strtotime("+ ".($uke - $denneUka)." week +4 day"));?></th>
		<th>L<span class="hidden-xs">ør<span class="hidden-sm">dag</span></span>&nbsp;<?php echo date('d/m', strtotime("+ ".($uke - $denneUka)." week +5 day"));?></th>
		<th>S<span class="hidden-xs">øn<span class="hidden-sm">dag</span></span>&nbsp;<?php echo date('d/m', strtotime("+ ".($uke - $denneUka)." week +6 day"));?></th>
	</tr>
<?php
	foreach (range(1, 4) as $vakttype){
?>
	<tr>
		<td><?php echo $vakttype; ?>.<span class="hidden-xs"> vakt</span></td>
		<td>Gauder</td>
		<td>Gauder</td>
		<td>Gauder</td>
		<td>Gauder</td>
		<td>Gauder</td>
		<td>Gauder</td>
		<td>Gauder</td>
	</tr>
<?php
	}
?>
</table>
	<?php
}

require_once('bunn.php');

?>