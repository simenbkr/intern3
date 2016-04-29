<?php

require_once('topp_utvalg.php');

?>

<div class="col-md-12">
	<h1>Utvalget &raquo; Regisjef &raquo; Arbeid</h1>
	<p>Gå til side: <?php

$lenker = array();
foreach (range($sideinndeling->getSider(), 1) as $side) {
	if ($side == $sideinndeling->getSide()) {
		$lenker[] = '<strong>' . $side . '</strong>';
	}
	else {
		$lenker[] = '<a href="?a=' . $cd->getBase() . 'utvalg/regisjef/arbeid/' . $side . '">' . $side . '</a>';
	}
}
echo implode(',' . PHP_EOL, $lenker);

?></p>
</div>

<div class="col-md-12 table-responsive">
	<table class="table table-striped table-hover">
		<tr>
			<th>Beboer</th>
			<th>Utført</th>
			<th>Kategori</th>
			<th>Tid brukt</th>
			<th>Kommentar</th>
			<th>Status</th>
			<th> </th>
		</tr>
<?php

foreach ($arbeidListe as $arbeid) {
	?>		<tr id="arbeid_<?php echo $arbeid->getId(); ?>">
			<td><?php echo $arbeid->getBruker()->getPerson()->getFulltNavn(); ?></td>
			<td><?php echo $arbeid->getTidUtfort(); ?></td>
			<td><?php

	if (!isset($oppg)) {
		if (get_class($arbeid->getPolymorfKategori()) == 'Oppgave') {
			echo '<a href="' . $cd->getBase(-2) . '/oppgave/' . $arbeid->getPolymorfKategori()->getId() . '">' . $arbeid->getPolymorfKategori()->getNavn() . '</a>';
		}
		else {
			echo $arbeid->getPolymorfKategori()->getNavn();
		}
	}

?></td>
			<td><?php echo $arbeid->getTidBrukt(); ?></td>
			<td><?php echo $arbeid->getKommentar(); ?></td>
			<td><?php echo $arbeid->getGodkjent() > 0 ? 'Godkjent' : 'Ubehandla'; ?></td>
			<td> </td>
		</tr>
<?php
}

?>
	</table>
</div>

<?php

require_once('bunn.php');

?>
