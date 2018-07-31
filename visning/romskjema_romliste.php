<?php

require_once('static/topp.php');

?>

<div class="col-md-12">
	<h1>Romskjema</h1>
	<p>Velg et rom:</p>
	<ul>
<?php
foreach ($romListe as $rom) {
	echo '	<li><a href="' . $base . $rom->getId() . '">' . $rom->getNavn() . '</a></li>' . PHP_EOL;
}
?>
	</ul>
</div>

<?php

require_once('static/bunn.php');

?>
