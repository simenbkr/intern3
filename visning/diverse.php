<?php

require_once('topp.php');

?>

<div class="col-md-6 col-sm-12">
	<h1>Datoer framover</h1>
	<p><span style="color: #0A0;">Du skal ikke sitte vakter</span>, evt <span style="color: #0A0;">Dine vakter</span>, <span style="color: #00A;">Ledige vakter</span>, <span style="color: #AA0;">Bursdager</span>, <span style="color: #A00;">Andre viktige datoer</span></p>
	<table class="table-bordered table">
		<tr>
			<th>U<span class="hidden-sm hidden-xs">ke</span></th>
			<th>M<span class="hidden-sm hidden-xs">andag</span></th>
			<th>T<span class="hidden-sm hidden-xs">irsdag</span></th>
			<th>O<span class="hidden-sm hidden-xs">nsdag</span></th>
			<th>T<span class="hidden-sm hidden-xs">orsdag</span></th>
			<th>F<span class="hidden-sm hidden-xs">redag</span></th>
			<th>L<span class="hidden-sm hidden-xs">ørdag</span></th>
			<th>S<span class="hidden-sm hidden-xs">øndag</span></th>
		</tr>
<?php

$denneManed = date('m');
$detteAr = date('Y');
$manedSlutt = strtotime('this month, midnight');

foreach (range($denneManed, $denneManed > 6 ? 12 : 6) as $maned) {
	$manedStart = $manedSlutt;
	$manedSlutt = strtotime('next month', $manedStart);
?>
		<tr>
			<th colspan="8"><?php echo date('F', $manedStart); ?></th>
<?php
	$dag = strtotime('last Monday', $manedStart);
	$ant = 0;
	do {
		if ($ant++ % 7 == 0) {
?>
		</tr>
		<tr>
			<td><?php echo date('W', $dag); ?></td>
<?php
		}
?>
			<td<?php
		if ($dag < $manedStart || $manedSlutt <= $dag) {
			echo ' style="color: #AAA;"';
		}
?>><?php echo date('j', $dag); ?></td>
<?php
	} while (($dag = strtotime('next day', $dag)) < $manedSlutt || $ant % 7 <> 0);
?>
		</tr>
<?php
}

?>
	</table>
</div>

<div class="col-md-6 col-sm-12">
	<h1>Regioppgaver</h1>
	<table class="table-bordered table">
	
	</table>
</div>

<?php

require_once('bunn.php');

?>