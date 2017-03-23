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

function underkjenn(id){
    $.ajax({
        type: 'POST',
        url: '?a=utvalg/regisjef/arbeid/tilbakemelding/' + id,
        data: 'underkjenn=1',
        success: function(data) {
            location.reload();
        },
        error: function(req, stat, err) {
            alert(err);
        }
    });
}

</script>

<div class="col-md-12">
	<h1>Utvalget &raquo; Regisjef &raquo; Arbeid</h1>
	Dette semesteret har Singsaker benyttet <b><?php echo $timer_brukt[0];?></b> (godkjente) av totalt <b><?php echo $timer_brukt[1] . ":00";?></b> regitimer.
	<?php if(isset($endret)){ ?>
		<div class="alert alert-success fade in" id="success" style="display:table; margin: auto; margin-top: 5%">
			<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
			Du endret et arbeid!
		</div>
	<?php
	}
	?>
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
