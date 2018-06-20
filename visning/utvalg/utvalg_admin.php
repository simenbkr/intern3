<?php

require_once('topp_utvalg.php');

?>

<div class="col-md-12">
	<h1>Utvalget &raquo; Admin</h1>
	<p>Velg en bruker du vil logge inn som:</p>
	<p><?php

foreach ($personListe as $person) {
	echo '<a href="' . $cd->getBase() . 'utvalg/admin/' . $person->getBrukerId() . '">' . $person->getFulltNavn() . '</a><br>' . PHP_EOL;
}

?></p>
</div>

<?php

require_once(__DIR__ . '/../static/bunn.php');

?>
