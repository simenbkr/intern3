<?php

require_once('topp_utvalg.php');

?>

<div class="col-md-12">
	<h1>Utvalget &raquo; Vaktsjef &raquo; Vaktoversikt</h1>

  <p><h2>Ufordelte vakter: <?php /*spørring*/ ?></h2></p>

  <p>Liste over vakter som folk har sittet</p>

</div>

<div class="col-md-12">

  <table class="table table-bordered">
		<tr>
			<th>Navn</th>
			<th>Totalt</th>
      <th>Vanlige</th>
			<th>Natt</th>
			<th>Helg</th>
			<th>Natthelg</th>
			<th>Igjen</th>
		</tr>

<?php

foreach ($beboerListe as $beboer){
	?>
		<tr>
			<td><a href="?a=beboer/<?php echo $beboer->getId(); ?>"><?php echo $beboer->getFulltNavn(); ?></a></td>
			<td><?php /*spørring*/ ?></td>
			<td><?php /*spørring*/ ?></td>
			<td><?php /*spørring*/ ?></td>
			<td><?php /*spørring*/ ?></td>
      <td><?php /*spørring*/ ?></td>
      <td><?php /*spørring*/ ?></td>
		</tr>
<?php
	}

?>
	</table>

</div>

<?php

require_once('bunn.php');

?>
