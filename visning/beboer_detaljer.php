<?php

require_once('topp.php');

?>

<div class="col-md-4">
	<h1><?php echo $beboer->getFulltNavn(); ?></h1>
	<p>[ <a href="?a=beboer">Beboerliste</a> ] [ <a href="?a=beboer/utskrift">Utskriftsvennlig</a> ] [ <a href="?a=beboer/statistikk">Statistikk</a> ]</p>
	<table class="table table-bordered">
		<tr>
			<th>Rom</th>
			<td><?php echo $beboer->getRom()->getNavn(); ?></td>
		</tr>
		<tr>
			<th>Telefon</th>
			<td><?php echo $beboer->getTelefon(); ?></td>
		</tr>
		<tr>
			<th>Epost</th>
			<td><a href="mailto:<?php echo $beboer->getEpost(); ?>"><?php echo $beboer->getEpost(); ?></a></td>
		</tr>
		<tr>
			<th>Studie</th>
			<td><?php
	$studie = $beboer->getStudie();
	$skole = $beboer->getSkole();
	if ($studie == null || $skole == null) {
		echo ' ';
	}
	else {
		echo $beboer->getKlassetrinn();
		?>. <a href="?a=studie/<?php echo $studie->getId(); ?>"><?php echo $studie->getNavn(); ?></a> (<a href="?a=skole/<?php echo $skole->getId(); ?>"><?php echo $skole->getNavn(); ?></a>)<?php
	}
	?></td>
		</tr>
		<tr>
			<th>Født</th>
			<td><?php echo $beboer->getFodselsdato(); ?></td>
		</tr>
		<tr>
			<th>Rolle</th>
			<td>-</td>
		</tr>
		<tr>
			<th>Antall semestre</th>
			<td><?php echo $beboer->getRomhistorikk()->getAntallSemestre(); ?></td>
		</tr>
	</table>
</div>

<?php

require_once('bunn.php');

?>
