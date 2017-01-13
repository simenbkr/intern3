<?php

require_once('topp.php');

?>

<div class="col-md-12">
	<h1>Beboer &raquo; Beboerliste</h1>
	<p>[ Beboerliste ] [ <a href="<?php echo $cd->getBase(); ?>beboer/utskrift">Utskriftsvennlig</a> ] [ <a href="<?php echo $cd->getBase(); ?>beboer/statistikk">Statistikk</a> ]</p>
	<table class="table table-bordered table-responsive">
		<tr>
			<th>Navn</th>
			<th>Rom</th>
			<th>Telefon</th>
			<th>Epost</th>
			<th>Studie</th>
			<th>Født</th>
			<th>Rolle</th>
		</tr>
<?php

foreach ($beboerListe as $beboer){
	if($beboer == null || !isset($beboer)){
		continue;
	}
	?>
		<tr>
			<td><a href="<?php echo $cd->getBase(); ?>beboer/<?php echo $beboer->getId(); ?>"><?php echo $beboer->getFulltNavn(); ?></a></td>
			<td><?php echo $beboer->getRom()->getNavn(); ?></td>
			<td><?php echo $beboer->getTelefon(); ?></td>
			<td><a href="mailto:<?php echo $beboer->getEpost(); ?>"><?php echo $beboer->getEpost(); ?></a></td>
			<td><?php
	$studie = $beboer->getStudie();
	$skole = $beboer->getSkole();
	if ($studie == null || $skole == null) {
		echo ' ';
	}
	else {
		echo $beboer->getKlassetrinn();
		?>. <a href="<?php echo $cd->getBase(); ?>studie/<?php echo $studie->getId(); ?>"><?php echo $studie->getNavn(); ?></a>&nbsp;(<a href="<?php echo $cd->getBase(); ?>skole/<?php echo $skole->getId(); ?>"><?php echo $skole->getNavn(); ?></a>)<?php
	}
	?></td>
			<td><?php echo $beboer->getFodselsdato(); ?></td>
			<td><?php
	$utvalgVervListe = $beboer->getUtvalgVervListe();
	if (count($utvalgVervListe) == 0) {
		echo str_replace(' ', '&nbsp;', $beboer->getRolle()->getNavn());
	}
	else {
		echo '<strong>' . $utvalgVervListe[0]->getNavn() . '</strong>';
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
