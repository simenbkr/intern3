<?php

require_once('topp.php');

?>

<div class="col-md-12">
	<h1>Vakt &raquo; Vaktbytte</h1>
	<p>[ <a href="?a=vakt">Vaktliste</a> ] [ Vaktbytte ]</p>
</div>
<div class="col-md-3 col-sm-6 col-sx-12">
  <table class="table table-bordered">
    <tr>
      <th>Dine vakter</th>
    </tr>
<?php

foreach (intern3\VaktListe::medBrukerId($cd->getAktivBruker()->getId()) as $vakt) {
  $tid = strtotime($vakt->getDato());
  $tekst = $vakt->getVakttype() . '. vakt ' . strftime('%A %d/%m', $tid);
  ?>    <tr>
<?php
if ($vakt->erFerdig()) {
  ?>
      <td class="celle_graa"><?php echo $tekst; ?></td>
<?php
}
else {
  ?>      <td><?php echo $tekst; ?>
        <input class="btn btn-info pull-right" type="button" value="Bytt">
      </td>
<?php
}
?>
    </tr>
<?php
  }
?>
  </table>
</div>
<div class="col-md-12"> </div>
<?php

foreach (range(1, 4) as $vakttype) {
	?><div class="col-md-3 col-sm-6 col-sx-12">
	<table class="table table-bordered">
		<tr>
			<th><?php echo $vakttype; ?>.&nbsp;vakt</th>
		</tr>
<?php
	foreach ($vaktbytteListe[$vakttype] as $vaktbytte) {
		?>		<tr>
			<td><input type="button" class="btn btn-info pull-right" value="Bytt"><?php
		echo '<strong>' . ucfirst(strftime('%A %d/%m', strtotime($vaktbytte->getVakt()->getDato()))) . '</strong>';
		echo '<br>';
		echo $vaktbytte->getVakt()->getBruker()->getPerson()->getFulltNavn();
		?></td>
		</tr><?php
	}
	?>	</table>
</div>
<?php
}

?>

<?php

require_once('bunn.php');

?>
