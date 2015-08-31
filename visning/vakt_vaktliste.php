<?php

require_once('topp.php');

$denneUka = @date('W');
$detteAret = @date('Y');

foreach (range($denneUka, $denneUka > 26 ? 52 : 26) as $uke){
?>
<table class="table-bordered table">
	<tr>
		<th>Uke <?php echo $uke; ?></th>
		<th>Mandag <?php echo @date('d/m', strtotime("+ ".($uke - $denneUka)." week"));?></th>
		<th>Tirsdag <?php echo @date('d/m', strtotime("+ ".($uke - $denneUka)." week +1 day"));?></th>
		<th>Onsdag <?php echo @date('d/m', strtotime("+ ".($uke - $denneUka)." week +2 day"));?></th>
		<th>Torsdag <?php echo @date('d/m', strtotime("+ ".($uke - $denneUka)." week +3 day"));?></th>
		<th>Fredag <?php echo @date('d/m', strtotime("+ ".($uke - $denneUka)." week +4 day"));?></th>
		<th>Lørdag <?php echo @date('d/m', strtotime("+ ".($uke - $denneUka)." week +5 day"));?></th>
		<th>Søndag <?php echo @date('d/m', strtotime("+ ".($uke - $denneUka)." week +6 day"));?></th>
	</tr>
<?php
	foreach (range(1, 4) as $vakttype){
?>
	<tr>
		<td><?php echo $vakttype; ?>. vakt</td>
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