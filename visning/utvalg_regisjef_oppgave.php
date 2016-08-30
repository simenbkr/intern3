<?php

require_once('topp_utvalg.php');

?>

<div class="col-md-12">
	<h1>Utvalget &raquo; Regisjef &raquo; Oppgave</h1>
</div>

<script>
function fjern(id) {
	$.ajax({
		type: 'POST',
		url: '<?php echo $_SERVER['REQUEST_URI']; ?>',
		data: id, // TODO må fikses!
		success: function(data) {
			$('#oppgave_' + id).html(data);
		},
		error: function(req, stat, err) {
			alert(err);
		}
	});
}

function godkjenn(id) {
	$.ajax({
		type: 'POST',
		url: '<?php echo $_SERVER['REQUEST_URI']; ?>',
		data: id, // TODO må fikses!
		success: function(data) {
			$('#oppgave_' + id).html(data);
		},
		error: function(req, stat, err) {
			alert(err);
		}
	});
}

function byttPolymorfkategori(id) {
	$('#polymorfkategori_ymse').hide();
	$('#polymorfkategori_feil').hide();
	$('#polymorfkategori_rapp').hide();
	$('#polymorfkategori_oppg').hide();
	switch (id) {
		case 'ymse':
			$('#polymorfkategori_ymse').show();
			break;
		case 'feil':
			$('#polymorfkategori_feil').show();
			break;
		case 'rapp':
			$('#polymorfkategori_rapp').show();
			break;
		case 'oppg':
			$('#polymorfkategori_oppg').show();
			break;
	}
}
$(document).ready(function() {
	byttPolymorfkategori('<?php echo isset($_POST['polymorfkategori_velger']) ? $_POST['polymorfkategori_velger'] : 'ymse'; ?>');
});

</script>

<div class="col-md-6 col-sm-12">
	<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
		<table class="table table-bordered">
      <tr>
        <th>Navn</th>
        <td><input name="navn" <?php echo isset($_POST['navn']) ? ' value="' . $_POST['navn'] . '"' : ''; ?>></td>
      </tr>
      <tr>
        <th>Prioritet</th>
        <td><input name="prioritet" <?php echo isset($_POST['prioritet']) ? ' value="' . $_POST['prioritet'] . '"' : ''; ?>></td>
      </tr>
			<tr>
				<th>Anslag timer</th>
				<td><input name="timer" placeholder="0:00"<?php echo isset($_POST['timer']) ? ' value="' . $_POST['timer'] . '"' : ''; ?>></td>
			</tr>
      <tr>
        <th>Anslag personer</th>
        <td><input name="personer" <?php echo isset($_POST['personer']) ? ' value="' . $_POST['personer'] . '"' : ''; ?>></td>
      </tr>
			<tr>
				<th>Beskrivelse</th>
				<td><textarea name="beskrivelse" cols="50" rows="5"><?php echo isset($_POST['beskrivelse']) ? $_POST['beskrivelse'] : ''; ?></textarea></td>
			</tr>
			<tr>
				<td> </td>
				<td><input type="submit" class="btn btn-primary" name="registrer" value="Registrer"></td>
			</tr>
		</table>
	</form>
</div>

<div class="col-md-12 table-responsive">
	<table class="table table-striped table-hover">
		<thead>
			<tr>
        <th>Navn</th>
        <th>Prioritet</th>
				<th>Anslag timer</th>
				<th>Anslag personer</th>
        <th>Påmeldte</th>
        <th>Beskrivelse</th>
        <th>Opprettet</th>
        <th>Godkjent</th>
				<th>Godkjenn/Fjern</th>
			</tr>
		</thead>
		<tbody>
<?php

foreach ($oppgaveListe as $oppgave) {
	?>			<tr id="<?php echo $oppgave->getId(); ?>">
				<td><?php echo $oppgave->getNavn(); ?></td>
			  <td><?php echo $oppgave->getPrioritetId(); ?></td>
				<td><?php echo $oppgave->getAnslagTimer(); ?></td>
				<td><?php echo $oppgave->getAnslagPersoner(); ?></td>
				<td><?php null == null ? '':''; ?></td>
				<td><?php echo htmlspecialchars($oppgave->getBeskrivelse()); ?></td>
        <td><?php echo $oppgave->getTidOppretta(); ?></td>
        <td><?php echo $oppgave->getTidGodkjent() != null ? '<span title="Godkjent av ' . intern3\Bruker::medId($oppgave->getGodkjentBrukerId()) == null ? intern3\Bruker::medId($oppgave->getGodkjentId())->getPerson()->getFulltNavn() : intern3\Beboer::medId($oppgave->getGodkjentBrukerId())->getFulltNavn() . ' > ' . $oppgave->getTidGodkjent() . '</span>' : ''; ?></td>
        <td><?php echo $oppgave->getGodkjent() > 0 ? '' : '<button onclick="godkjenn(' . $oppgave->getId() . ')">&#x2714;</button>'; ?> <button onclick="fjern(<?php $oppgave->getId(); ?>)">&#x2718;</button></td>
			</tr>
<?php
}
?>
		</tbody>
	</table>
</div>

<?php

require_once('bunn.php');

?>
