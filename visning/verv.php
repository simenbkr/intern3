<?php

require_once('topp.php');

?>
<div class="col-md-12">
	<h1>Verv</h1>
	<table class="table table-bordered" id="tabellen">
        <thead>
		<tr>
			<th>Åpmandsverv</th>
			<th>Åpmand/åpmend</th>
			<th>Epost</th>
		</tr>
        </thead>
    <tbody>
<?php

foreach ($vervListe as $verv) {
	?>		<tr>
			<td><a href="?a=verv/<?php echo $verv->getId();?>"><?php echo $verv->getNavn(); ?></a></td>
			<td><?php
	$i = 0;
	foreach ($verv->getApmend() as $apmand) {
		if ($i++ > 0) {
			echo ', ';
		}
		echo '<a href="?a=beboer/' . $apmand->getId() . '">' . $apmand->getFulltNavn() . '</a>';
	}
	?></td>
			<td><?php
	$epost = $verv->getEpost();
	if ($verv == null) {
		echo ' ';
	}
	else {
		echo '<a href="mailto:' . $epost . '">' . $epost . '</a>';
	}
	?></td>
		</tr><?php
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
