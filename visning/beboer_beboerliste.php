<?php

require_once('topp.php');

?>

<div class="col-md-12">
	<h1>Beboerliste</h1>
	<table class="table-bordered table">
		<tr>
			<th>Navn</th>
			<th>Rom</th>
			<th>Telefon</th>
			<th>Epost</th>
			<th>Rolle</th>
		</tr>
<?php

foreach ($beboerListe as $beboer){
?>
		<tr>
			<td><a href="?a=beboer/<?php echo $beboer->getId(); ?>"><?php echo $beboer->getFulltNavn(); ?></a></td>
			<td><?php echo $beboer->getRom()->getNavn(); ?></td>
			<td><?php echo $beboer->getTelefon(); ?></td>
			<td><a href="mailto:<?php echo $beboer->getEpost(); ?>"><?php echo $beboer->getEpost(); ?></a></td>
			<td>-</td>
		</tr>
<?php
	}

?>
	</table>
</div>

<?php

require_once('bunn.php');

?>