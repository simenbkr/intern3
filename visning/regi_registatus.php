<?php

require_once('topp_utvalg.php');

?>
<script>



	function endreRegi(){
		var halv = document.getElementById("1").value;
		var full = document.getElementById("3").value;
		$.ajax({
			type: 'POST',
			url: '?a=utvalg/regisjef/registatus',
			data: 'endreRegi=1&halv=' + halv + "&full=" + full,
			method: 'POST',
			success: function (data) {
				location.reload();
			},
			error: function (req, stat, err) {
				alert(err);
			}
		});
	}

    $(document).ready(function(){
        $('#tabellen').DataTable({
            "paging": false,
            "searching": false
        });

    });
</script>
<div class="col-md-12">
	<h1>Regi &raquo; Registatus</h1>
	<div class="col-md-6" id="kake">
		<table class="table table-bordered table-responsive small">
			<tr>
				<th>Rolle</th>
				<th>Regitimer</th>
			</tr>
			<?php foreach($roller as $rollen){
				if($rollen->getNavn() == "Full vakt") {continue;} ?>
				<tr>
					<td><?php echo $rollen->getNavn();?></td>
					<td><input type="text" name="host" id="<?php echo $rollen->getId();?>" value="<?php echo $rollen->getRegitimer();?>" size="1"></td>
				</tr>
				<?php
			}
			?>
			<tr>
				<td></td>
				<td><button class="btn btn-primary btn-sm" onclick="endreRegi()">Endre</button></td>
			</tr>
		</table>
	</div>
	Dette semesteret har Singsaker benyttet <b><?php echo ($timer_brukt[0] != null ? $timer_brukt[0] : '00:00');?></b> (godkjente) av totalt <b><?php echo ($timer_brukt[1] != null) ? $timer_brukt[1] . ":00" : '??';?></b> regitimer. <hr>
<?php

$totaltTildelt = 0;
$totaltUtfort = 0;
$totaltIgjen = 0;

foreach ($tabeller as $tittel => $brukere) {
	?>	<h2><?php echo $tittel; ?></h2>
	<table class="table table-bordered" id="tabellen" data-toggle="table">
		<thead>
			<tr>
				<th>Navn</th>
				<th>Rom</th>
				<th>Rolle</th>
				<th>Regitimer</th>
				<th>Utført</th>
				<th>Igjen</th>
			</tr>
		</thead>
		<tbody>
<?php

foreach ($brukere as $bruker) {
	$beboer = $bruker->getPerson();
	$tildelt = $beboer->getRolle()->getRegitimer();
	$utfort = $bruker->getRegisekunderMedSemester() / 3600;
	$igjen = $tildelt - $utfort;
	$totaltTildelt += $tildelt;
	$totaltUtfort += $utfort;
	$totaltIgjen += $igjen;
	?>
			<tr>
				<td><?php echo $beboer->getFulltNavn(); ?></td>
				<td><?php echo $beboer->getRom()->getNavn(); ?></td>
				<td><?php echo $beboer->getRolle()->getNavn(); ?></td>
				<td><?php echo $tildelt; ?></td>
				<td><?php echo intern3\Funk::timerTilTidForm($utfort); ?></td>
				<td><?php echo intern3\Funk::timerTilTidForm($igjen); ?></td>
			</tr>
<?php
	}

?>
		</tbody>
	</table>
<?php
}

?>
	<p>Totalt <strong><?php echo intern3\Funk::timerTilTidForm($totaltUtfort); ?></strong> timer utført og <strong><?php echo intern3\Funk::timerTilTidForm($totaltIgjen); ?></strong> igjen av <strong><?php echo intern3\Funk::timerTilTidForm($totaltTildelt); ?></strong> tildelte.</p>
</div>
<link rel="stylesheet" type="text/css" href="css/dataTables.css"/>
<script type="text/javascript" src="js/dataTables.js"></script>
<?php

require_once('bunn.php');

?>
