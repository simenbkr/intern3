<?php

require_once('topp.php');

?>

<div class="col-md-12">
	<h1>Utleier</h1>
	<table class="table-bordered table">
		<tr>
			<th>Dato</th>
			<th>Rom</th>
			<th>Navn</th>
			<th>Barvakter</th>
			<th>Vasking</th>
		</tr>

<?php

foreach (/*$utleie as $utleiet*/range(1, 5) as $i){

?>
		<tr>
			<td>dato</td>
			<td>bodegaen</td>
			<td>eksempel</td>
			<td><?php
					// if ($utleiet->getRom() == "Bodegaen";) {
						if (/*empty($utleiet->getBeboer1_id())*/True) {
							?>
							<INPUT TYPE = "Submit" Name = "Submit1" VALUE = "Barvakt">
							<?php
						}
						?>
						<!-- <p></p> -->
						<?php
						if (/*empty($utleiet->getBeboer2_id())*/True){
							?>
							<INPUT TYPE = "Submit" Name = "Submit2" VALUE = "Barvakt">
							<?php
						}
						else {
							// echo $utleiet->getBeboer1_id(); echo $utleiet->getBeboer2_id();
						}
					// }
					?></td>
			<td><?php
					if (/*empty($utleiet->getBeboer3_id())*/True) {
							?>
							<INPUT TYPE = "Submit" Name = "Submit3" VALUE = "Vasking">
							<?php
					}
					else {
							// echo $utleiet->getBeboer3_id();
					}
					?></td>
		</tr>
<?php
}
?>
	</table>
</div>

<?php

require_once('bunn.php');

?>
