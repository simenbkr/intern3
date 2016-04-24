<?php

require_once('topp.php');

?>

<div class="col-md-12">
	<h1>Romskjema (<?php echo $rom->getNavn(); ?>)</h1>
	<p>Her registreres feil på rommet. Hvis et punkt er i orden, så la være å skrive noe på det. Se også bort fra bad hvis rommet ditt ikke har det.</p>
<?php

if ($tvungen) {
	echo '	<p style="color: #F00;">Du må ta stilling til dette skjemaet i starten av hvert semester.</p>' . PHP_EOL;
}

?>
</div>

<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
<?php

foreach (intern3\FeilkategoriListe::alle() as $fk) {
	?>	<div class="col-md-6 col-sm-12 romskjemaseksjon">
		<h2><?php echo $fk->getNavn(); ?></h2>
<?php

foreach ($fk->getFeilListe() as $f) {
	?>		<h3><?php echo $f->getNavn(); ?></h3>
<?php

if ($f->getBeskrivelse() <> null) {
	echo '		<p class="liten">(' . $f->getBeskrivelse() . ')</p>' . PHP_EOL;
}

?>
		<p><input type="text" class="form-control" name="feil[<?php echo $f->getId(); ?>]" placeholder="Noe galt som ikke er rapportert tidligere?"></p>
<?php
	$rapporter = $f->getLikeUlosteRapporterListe($rom);
	if (count($rapporter) > 0) {
		echo '		<p>Tidligere tilbakemeldinger:</p>' . PHP_EOL . '		<ul>' . PHP_EOL;
		foreach ($rapporter as $r) {
			echo '			<li>' . $r->getMerknad() . ' <span class="liten">(' . $r->getBruker()->getPerson()->getFulltNavn() . ', ' . $r->getTidOppretta() . ')</span></li>' . PHP_EOL;
		}
		echo '		</ul>' . PHP_EOL;
	}
}

?>
	</div>
<?php
}

?>
	<div class="col-md-12">
		<p><input type="submit" value="Send romskjema"></p>
	</div>
</form>
<?php

require_once('bunn.php');

?>
