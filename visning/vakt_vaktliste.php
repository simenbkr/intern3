<?php

require_once('topp.php');

?>
<div class="col-md-12">
	<h1>Vaktliste</h1>
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

?>
</div>
<?php

require_once('bunn.php');

?>
