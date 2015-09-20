<?php

require_once('topp.php');

$beboer = $cd->getAktivBruker()->getPerson();

?>
<div class="col-md-12">
	<h1>Profil</h1>
<?php

if (count($feil) > 0) {
	echo '	<ul style="color: #900;">' . PHP_EOL;
	foreach ($feil as $pkt) {
		echo '		<li>' . $pkt . '</li>' . PHP_EOL;
	}
	echo '	</ul>' . PHP_EOL;
}
else {
	echo '	<p>[ <a href="?a=beboer/' . $beboer->getId() . '">Se profil</a> ]</p>
';
}

?>
</div>
<div class="col-md-4">
	<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
		<h2>Generell info<input type="hidden" name="endre" value="generell"></h2>
		<table class="table table-bordered">
			<tr>
				<th>Fødselsdato</th>
				<td><input type="text" name="fodselsdato" size="10" value="<?php echo isset($_POST['fodselsdato']) ? $_POST['fodselsdato'] : $beboer->getFodselsdato(); ?>"><br>(åååå-mm-dd)</td>
			</tr>
			<tr>
				<th>Epost</th>
				<td><input type="text" name="epost" value="<?php echo isset($_POST['epost']) ? $_POST['epost'] : $beboer->getEpost(); ?>"></td>
			</tr>
			<tr>
				<th>Telefon</th>
				<td><input type="text" name="telefon" size="12" value="<?php echo isset($_POST['telefon']) ? $_POST['telefon'] : $beboer->getTelefon(); ?>"></td>
			</tr>
			<tr>
				<th>Adresse</th>
				<td><input type="text" name="adresse" value="<?php echo isset($_POST['adresse']) ? $_POST['adresse'] : $beboer->getAdresse(); ?>"></td>
			</tr>
			<tr>
				<th>Postnummer</th>
				<td><input type="text" name="postnummer" size="4" value="<?php echo isset($_POST['postnummer']) ? $_POST['postnummer'] : $beboer->getPostnummer(); ?>"></td>
			</tr>
			<tr>
				<th>Skole</th>
				<td><select name="skole_id">
					<option value="0">- velg -</option>
<?php

$skoleId = isset($_POST['skole_id']) && Skole::medId($_POST['skole_id']) <> null ? $_POST['skole_id'] : $beboer->getSkoleId();
foreach (intern3\SkoleListe::alle() as $skole) {
	echo '					<option value="' . $skole->getId() . '"';
	if ($skole->getId() == $skoleId) {
		echo ' selected="selected"';
	}
	echo '>' . $skole->getNavn() . '</option>' . PHP_EOL;
}

?>
				</select></td>
			</tr>
			<tr>
				<th>Studie</th>
				<td><select name="studie_id">
					<option value="0">- velg -</option>
<?php

$studieId = isset($_POST['studie_id']) && Studie::medId($_POST['studie_id']) <> null ? $_POST['studie_id'] : $beboer->getStudieId();
foreach (intern3\StudieListe::alle() as $studie) {
	echo '					<option value="' . $studie->getId() . '"';
	if ($studie->getId() == $studieId) {
		echo ' selected="selected"';
	}
	echo '>' . $studie->getNavn() . '</option>' . PHP_EOL;
}

?>
				</select></td>
			</tr>
			<tr>
				<th>Klassetrinn</th>
				<td><input type="text" name="klassetrinn" size="1" value="<?php echo isset($_POST['klassetrinn']) ? $_POST['klassetrinn'] : $beboer->getKlassetrinn(); ?>"></td>
			</tr>
		</table>
		<p><input type="submit" class="btn-primary" value="Lagre"></p>
	</form>
</div>
<div class="col-md-4">
	<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
		<h2>Endre passord<input type="hidden" name="endre" value="passord"></h2>
		<p style="color: #990;">Obs: Det anbefales å bruke et sterkt passord, særlig om man er utvalgsmedlem og har admintilgang. Minstekravet er en lengde på 8 tegn.</p>
		<table class="table table-bordered">
			<tr>
				<th>Nytt passord</th>
				<td><input type="password" name="passord1"></td>
			</tr>
			<tr>
				<th>Gjenta passord</th>
				<td><input type="password" name="passord2"></td>
			</tr>
		</table>
		<p><input type="submit" class="btn-primary" value="Lagre"></p>
	</form>
</div>
<div class="col-md-4">
	<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
		<h2>Bytt profilbilde<input type="hidden" name="endre" value="bilde"></h2>
		<p style="color: #990;">Du har for øyeblikket ikke noe profilbilde.</p>
		<p style="color: #900;">Hakke implementert dette heller.</p>
		<p><input type="submit" class="btn-primary" value="Lagre"></p>
	</form>
</div>
<div class="col-md-4">
	<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
		<h2>Varsler<input type="hidden" name="endre" value="varsler"></h2>
		<table class="table table-bordered">
			<tr>
				<th colspan="2">Send epost når ...</th>
			</tr>
			<tr>
				<td>noen vil bytte eller gi bort en vakt</td>
				<td><input type="checkbox" name="varsel[vaktbytte]" value="1"></td>
			</tr>
		</table>
		<p><input type="submit" class="btn-primary" value="Lagre"></p>
	</form>
</div>
<?php

require_once('bunn.php');

?>
