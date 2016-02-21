<?php

require_once('topp_utvalg.php');

?>

<div class="col-md-12">
	<h1>Utvalget &raquo; Vaktsjef &raquo; Vaktoversikt</h1>

  <p><h2>Antall vakter: <?php echo $antallVakter; ?></h2></p>
  <p><h2>Ufordelte vakter: <?php echo $antallUfordelte; ?></h2></p>
  <p><h2>Ubekreftede vakter: <?php echo $antallUbekreftet; ?></h2></p>

</div>

<div class="col-md-12">

  <table class="table table-bordered">
		<tr>
			<th>Navn</th>
			<th>Straffevakter</th>
      <th>Skal sitte</th>
			<th>Har sittet</th>
			<th>Er oppsatt</th>
			<th>Har igjen</th>
			<th>Ikke oppsatt</th>
      <th>Ikke bekreftet</th>
		</tr>

<?php
foreach ($beboerListe as $beboer){
  ?>
		<tr>
			<td><a href="?a=beboer/<?php echo $beboer->getId(); ?>"><?php echo $beboer->getFulltNavn(); ?></a></td>
			<td><?php echo $beboer->antallStraffevakter(); ?></td>
			<td><?php echo $beboer->antallVakterSkalSitte(); ?></td>
			<td><?php echo $beboer->antallVakterHarSittet(); ?></td>
			<td><?php echo $beboer->antallVakterErOppsatt(); ?></td>
      <td><?php echo $beboer->antallVakterHarIgjen(); ?></td>
      <td><?php echo $beboer->antallVakterIkkeOppsatt(); ?></td>
      <td><?php echo $beboer->antallVakterIkkeBekreftet(); ?></td>
		</tr>
<?php
	}

?>
	</table>

</div>

<?php

require_once('bunn.php');

?>
