<?php

require_once('topp.php');

?>

<div class="col-md-12">
	<h1>Statistikk</h1>
	<p>[ <a href="?a=beboer">Beboerliste</a> ] [ <a href="?a=beboer/utskrift">Utskriftsvennlig</a> ] [ Statistikk ]</p>
</div>
<?php

foreach ($histogram as $navn => $hist) {
	?><div class="col-md-12">
	<h2><?php echo $navn; ?></h2>
<?php
	include('histogram.php');
	?></div>
<?php
}

require_once('bunn.php');

?>
