<?php

require_once('topp.php');

?>

<div class="col-md-12">
	<h1>Regi &raquo; Oppgaver</h1>
</div>

<div class="col-md-12 table-responsive">
	<table class="table table-striped table-hover">
		<thead>
			<tr>
        <th>Navn</th>
        <th>Prioritet</th>
				<th>Anslag timer</th>
				<th>Anslag personer</th>
        <th>Beskrivelse</th>
				<!-- <th> </th> -->
			</tr>
		</thead>
		<tbody>
<?php

foreach ($oppgaveListe as $oppgave) {
	?>			<tr>
				<td><?php echo $oppgave->getNavn(); ?></td>
			  <td><?php echo $oppgave->getPrioritetId(); ?></td>
				<td><?php echo $oppgave->getAnslagTimer(); ?></td>
				<td><?php echo $oppgave->getAnslagPersoner(); ?></td>
				<td><?php echo htmlspecialchars($oppgave->getBeskrivelse()); ?></td>
				<!-- <td><input type="button" class="btn btn-sm btn-info" Name="btn1" value="Meld på"></td> -->
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
