<?php

require_once('topp_utvalg.php');

?>

<script>

function godkjennArbeid(id, underkjenn) {
	underkjenn = underkjenn || false;
	$.ajax({
		type: 'POST',
		url: '<?php echo $_SERVER['REQUEST_URI']; ?>',
		data: 'id=' + id + '&underkjenn=' + (underkjenn != false ? 1 : 0),
		success: function(data) {
			$('#arbeid_' + id).html(data);
		},
		error: function(req, stat, err) {
			alert(err);
		}
	});
}

</script>

<div class="col-md-12">
	<h1>Utvalget &raquo; Regisjef &raquo; Arbeid</h1>
	<p>Gå til side: <?php

$lenker = array();
foreach (range($sideinndeling->getSider(), 1) as $side) {
	if ($side == $sideinndeling->getSide()) {
		$lenker[] = '<strong>' . $side . '</strong>';
	}
	else {
		$lenker[] = '<a href="' . $cd->getBase() . 'utvalg/regisjef/arbeid/' . $side . '">' . $side . '</a>';
	}
}
echo implode(',' . PHP_EOL, $lenker);

?></p>
</div>

<div class="col-md-12 table-responsive">
	<table class="table table-striped table-hover">
		<thead>
			<tr>
				<th>Beboer</th>
				<th>Utført</th>
				<th>Kategori</th>
				<th>Tid brukt</th>
				<th>Kommentar</th>
				<th>Status</th>
				<th> </th>
			</tr>
		</thead>
		<tbody>
<?php

foreach ($arbeidListe as $arbeid) {
	echo '			<tr id="arbeid_' . $arbeid->getId() . '">' . PHP_EOL;
	include('utvalg_regisjef_arbeid_rad.php');
	echo '			</tr>' . PHP_EOL;
}

?>
		</tbody>
	</table>
</div>

<?php

require_once('bunn.php');

?>
