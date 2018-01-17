<?php

require_once('topp_utvalg.php');

?>

<div class="col-md-12">
	<h1>Utvalget &raquo; Romsjef &raquo; Beboerliste</h1>
	<p>[ Beboerliste ] [ <a href="<?php echo $cd->getBase(); ?>beboer/utskrift">Utskriftsvennlig</a> ] [ <a href="<?php echo $cd->getBase(); ?>beboer/statistikk">Statistikk</a> ]</p>
  <p> </p>

    <div class="col-sm-6">
        <table class="table table-responsive">
            <tr>
                <td>Antall beboere</td>
                <td><?php echo count($beboerListe); ?></td>
            </tr>

            <tr>
                <td>Full regi</td>
                <td><?php echo $fullregi; ?></td>
            </tr>

            <tr>
                <td>Full vakt</td>
                <td><?php echo $fullvakt; ?></td>
            </tr>

            <tr>
                <td>Halv vakt/halv regi</td>
                <td><?php echo $halv; ?></td>
            </tr>

        </table>
    </div>
    
    
	<table class="table-bordered table" id="tabellen">
        <thead>
		<tr>
			<th>Navn</th>
			<th>Rom</th>
			<th>Telefon</th>
			<th>Epost</th>
			<th>Studie</th>
			<th>Født</th>
			<th>Rolle</th>
		</tr>
        </thead>
        <tbody>
<?php

foreach ($beboerListe as $beboer){
	?>
		<tr>
			<td><a href="?a=utvalg/romsjef/beboerliste/<?php echo $beboer->getId(); ?>"><?php echo $beboer->getFulltNavn(); ?></a></td>
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
		?>. <a href="?a=studie/<?php echo $studie->getId(); ?>"><?php echo $studie->getNavn(); ?></a>&nbsp;(<a href="?a=skole/<?php echo $skole->getId(); ?>"><?php echo $skole->getNavn(); ?></a>)<?php
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
        </tbody>
	</table>
</div>

<link rel="stylesheet" type="text/css" href="css/dataTables.css"/>
<script type="text/javascript" src="js/dataTables.js"></script>

<script>

    $(document).ready(function () {
        var table = $('#tabellen').DataTable({
            "paging": false,
            "searching": false,
            //"scrollY": "500px"
        });
    });

</script>


<?php

require_once('bunn.php');

?>
