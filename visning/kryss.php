<?php

require_once('topp.php');

?>
<div class="col-md-12">
	<h1>Kryss</h1>
	<p>[ <a href="?a=kryss">Nylig kryssing</a> ] [ <a href="?a=kryss/statistikk">Statistikk</a> ]</p>
</div>
<div class="col-md-4">
	<h2>Ditt totale alkoholkonsum</h2>
	<table class="table table-bordered">
		<tr>
			<th>Drikke</th>
			<th>Antall</th>
		</tr>
<?php

foreach ($sumKryss as $navn => $antall) {
	?>		<tr>
			<td><?php echo $navn; ?></td>
			<td><?php echo $antall; ?></td>
		</tr>
<?php
}

?>
	</table>
</div>
<div class="col-md-4">
	<h2>Gjennom ukedagene (%)</h2>
<?php

$hist = $ukedager;
include('histogram.php');

?>
</div>
<?php

require_once('bunn.php');

?>
