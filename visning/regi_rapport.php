<?php

require_once('topp.php');

?>

<div class="col-sm-3 col-xs-12">
	<h2 data-toggle="collapse" data-target="#kollapsAnsvarsomrade" aria-expanded="false" aria-controls="kollapsAnsvarsomrade" style="cursor: pointer;">Ansvarsområder <span class="caret"></span></h2>
	<p id="kollapsAnsvarsomrade" class="collapse" style="width: 100%;">
<?php

foreach (intern3\AnsvarsomradeListe::alle() as $ao) {
	echo '		<a class="display: block;" href="?a=regi/rapport/ansvarsomrade/' . $ao->getId() . '">' . $ao->getNavn() . '</a><br>' . PHP_EOL;
}

?>
	</p>
	<h2 data-toggle="collapse" data-target="#kollapsFeilkategori" aria-expanded="false" aria-controls="kollapsFeilkategori" style="cursor: pointer;">Feilkategorier <span class="caret"></span></h2>
	<p id="kollapsFeilkategori" class="collapse" style="width: 100%;">
<?php

foreach (intern3\FeilkategoriListe::alle() as $fk) {
	echo '		<a class="display: block;" href="?a=regi/rapport/feilkategori/' . $fk->getId() . '">' . $fk->getNavn() . '</a><br>' . PHP_EOL;
}

?>
	</p>
	<h2 data-toggle="collapse" data-target="#kollapsRom" aria-expanded="false" aria-controls="kollapsRom" style="cursor: pointer;">Rom <span class="caret"></span></h2>
	<p id="kollapsRom" class="collapse" style="width: 100%;">
<?php

foreach (intern3\RomListe::alle() as $rom) {
	echo '		<a style="display: inline-block;" href="?a=regi/rapport/rom/' . $rom->getId() . '">' . $rom->getNavn() . '</a>' . PHP_EOL;
}

?>
	</p>
	<h2 data-toggle="collapse" data-target="#kollapsPrioritet" aria-expanded="false" aria-controls="kollapsPrioritet" style="cursor: pointer;">Prioriteter <span class="caret"></span></h2>
	<p id="kollapsPrioritet" class="collapse" style="width: 100%;">
<?php

foreach (intern3\PrioritetListe::alle() as $p) {
	echo '		<a class="display: block;" href="?a=regi/rapport/prioritet/' . $p->getId() . '">' . $p->getNavn() . '</a><br>' . PHP_EOL;
}

?>
	</p>
</div>

<div class="col-sm-9 col-xs-12">
	<h1>Regi &raquo; Rapporter<?php

if (count($ekstraTittel) > 0) {
	echo ' &raquo; ' . implode(' &raquo; ', $ekstraTittel);
}

?></h1>
<?php

if (count($rapportListe) > 0) {
	?>
	<div class="table-responsive">
		<table class="table table-striped table-hover">
			<thead>
				<tr>
					<th>Rom</th>
					<th>Prioritet</th>
					<th>Feil</th>
					<th>Merknad</th>
					<th>Oppretta</th>
					<th colspan="2"> </th>
				</tr>
			</thead>
<?php

foreach ($rapportListe as $rapport) {
	$rom = $rapport->getKvittering()->getRom();
	$prioritet = $rapport->getPrioritet();
	$feil = $rapport->getFeil();
	?>
			<tbody>
				<tr<?php if ($rapport->getGodkjent()) { echo ' style="opacity: .6; text-decoration: line-through;"'; } ?>>
					<td><a href="?a=<?php echo $cd->getBase() . '/rom/' . $rom->getId(); ?>"><?php echo $rom->getNavn(); ?></a></td>
					<td><a href="?a=<?php echo $cd->getBase() . '/prioritet/' . $prioritet->getId(); ?>" style="color: <?php echo $prioritet->getFarge(); ?>"><?php echo $prioritet->getNavn(); ?></a></td>
					<td><a href="?a=<?php echo $cd->getBase() . '/feil/' . $feil->getId(); ?>"><?php echo $feil->getNavn(); ?></a></td>
					<td><?php echo htmlspecialchars($rapport->getMerknad()); ?></td>
					<td><?php echo substr($rapport->getTidOppretta(), 0, 10); ?></td>
					<td>Detaljer</td>
					<td> </td>
				</tr>
			</tbody>
<?php
}

?>
		</table>
	</div>
<?php
}
else {
	echo '	<p>Velg en eller annen kategori for å se rapporterte feil.</p>' . PHP_EOL;
}


?>
</div>
</div>

<?php

require_once('bunn.php');

?>
