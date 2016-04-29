<?php

require_once('topp_utvalg.php');

?>

<div class="col-md-12">
	<h1>Utvalget &raquo; Vaktsjef &raquo; Generer vaktliste</h1>
	<p>Dette skjemaet brukes for å lage ny vaktliste. Den gamle vil dermed forsvinne helt. Man oppgir varighet for vaktlista, samt enkeltvakter og perioder hvor man vil tildele manuelt. Deretter vil de resterende vaktene bli fordelt tilfeldig mellom beboerne.</p>
</div>

<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">

<div class="col-md-4 col-sm-12">
<h2>Varighet for vaktlista</h2>
<?php

if (count($feilVarighet) > 0) {
	echo '	<ul style="color: #900;">' . PHP_EOL;
	foreach ($feilVarighet as $pkt) {
		echo '		<li>' . $pkt . '</li>' . PHP_EOL;
	}
	echo '	</ul>' . PHP_EOL;
}

?>
	<table class="table-bordered table">
		<tr>
			<th>Fra og med</th>
			<td>
				<select name="varighet_type_start">
					<option value="1"<?php if (isset($_POST['varighet_type_start']) && $_POST['varighet_type_start'] == '1') { echo ' selected="selected"'; } ?>>1. vakt</option>
					<option value="2"<?php if (isset($_POST['varighet_type_start']) && $_POST['varighet_type_start'] == '2') { echo ' selected="selected"'; } ?>>2. vakt</option>
					<option value="3"<?php if (isset($_POST['varighet_type_start']) && $_POST['varighet_type_start'] == '3') { echo ' selected="selected"'; } ?>>3. vakt</option>
					<option value="4"<?php if (isset($_POST['varighet_type_start']) && $_POST['varighet_type_start'] == '4') { echo ' selected="selected"'; } ?>>4. vakt</option>
				</select>
				<input class="datepicker" name="varighet_dato_start" size="8" placeholder="dato"<?php if (isset($_POST['varighet_dato_start']) && $_POST['varighet_dato_start']) { echo ' value="' . $_POST['varighet_dato_start'] . '"'; } ?>>
			</td>
		</tr>
		<tr>
			<th>Til og med</th>
			<td>
				<select name="varighet_type_slutt">
					<option value="1"<?php if (isset($_POST['varighet_type_slutt']) && $_POST['varighet_type_slutt'] == '1') { echo ' selected="selected"'; } ?>>1. vakt</option>
					<option value="2"<?php if (isset($_POST['varighet_type_slutt']) && $_POST['varighet_type_slutt'] == '2') { echo ' selected="selected"'; } ?>>2. vakt</option>
					<option value="3"<?php if (isset($_POST['varighet_type_slutt']) && $_POST['varighet_type_slutt'] == '3') { echo ' selected="selected"'; } ?>>3. vakt</option>
					<option value="4"<?php if (!isset($_POST['varighet_type_slutt']) || $_POST['varighet_type_slutt'] == '4') { echo ' selected="selected"'; } ?>>4. vakt</option>
				</select>
				<input class="datepicker" name="varighet_dato_slutt" size="8" placeholder="dato"<?php if (isset($_POST['varighet_dato_slutt']) && $_POST['varighet_dato_slutt']) { echo ' value="' . $_POST['varighet_dato_slutt'] . '"'; } ?>>
			</td>
		</tr>
		<tr>
			<th>Sikkerhetsmargin</th>
			<td colspan="2"><input type="text" name="varighet_sikkerhetsmargin" size="2" value="<?php echo isset($_POST['varighet_sikkerhetsmargin']) && $_POST['varighet_sikkerhetsmargin'] ? $_POST['varighet_sikkerhetsmargin'] : '2'; ?>"><br>Her menes det hvor mange vakter som ikke skal tildeles. De velges tilfeldig.</td>
		</tr>
	</table>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
	<script>
var enkeltvaktIterator = 0;
function flereEnkeltvakter() {
	id = enkeltvaktIterator++;
	$('#enkeltvakter').append('<tr><td><select name="enkeltvakt_type[' + id + ']"><option value="1">1. vakt</option><option value="2">2. vakt</option><option value="3">3. vakt</option><option value="4">4. vakt</option></select>&nbsp;<input class="datepicker" name="enkeltvakt_dato[' + id + ']" size="8" placeholder="dato"></td></tr>');
	formaterDatovelger();
}
$(flereEnkeltvakter);
	</script>
	<h2>Manuelle vakter</h2>
	<p>Her kan man oppgi juleball, helgavakter og andre generelt upassende vakter.</p>
<?php

if (count($feilEnkelt) > 0) {
	echo '	<ul style="color: #900;">' . PHP_EOL;
	foreach ($feilEnkelt as $pkt) {
		echo '		<li>' . $pkt . '</li>' . PHP_EOL;
	}
	echo '	</ul>' . PHP_EOL;
}

?>
	<table class="table-bordered table">
		<thead>
			<tr>
				<th><input type="button" class="btn btn-xs btn-info pull-right" value="Flere" onclick="flereEnkeltvakter();">Type og dato</th>
			</tr>
		</thead>
		<tbody id="enkeltvakter"></tbody>
	</table>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
	<script>
var vaktperiodeIterator = 0;
function flereVaktperioder() {
	id = vaktperiodeIterator++;
	$('#vaktperioder').append('<tr><td><select name="vaktperiode_type_start[' + id + ']"><option value="1">1. vakt</option><option value="2">2. vakt</option><option value="3">3. vakt</option><option value="4">4. vakt</option></select>&nbsp;<input class="datepicker" name="vaktperiode_dato_start[' + id + ']" size="8" placeholder="dato"></td><td><select name="vaktperiode_type_slutt[' + id + ']"><option value="1">1. vakt</option><option value="2">2. vakt</option><option value="3">3. vakt</option><option value="4" selected="selected">4. vakt</option></select>&nbsp;<input class="datepicker" name="vaktperiode_dato_slutt[' + id + ']" size="8" placeholder="dato"></td></tr>');
	formaterDatovelger();
}
$(flereVaktperioder);
	</script>
	<h2>Perioder med manuell tildeling</h2>
	<p>Her kan man oppgi eksamenstid, ferier o.l.</p>
<?php

if (count($feilPeriode) > 0) {
	echo '	<ul style="color: #900;">' . PHP_EOL;
	foreach ($feilPeriode as $pkt) {
		echo '		<li>' . $pkt . '</li>' . PHP_EOL;
	}
	echo '	</ul>' . PHP_EOL;
}

?>
  <table class="table-bordered table">
		<thead>
			<tr>
				<th>Fra og med</th>
				<th><input type="button" class="btn btn-xs btn-info pull-right" value="Flere" onclick="flereVaktperioder();">Til og med</th>
			</tr>
		</thead>
		<tbody id="vaktperioder"></tbody>
	</table>
</div>

<div class="col-md-12">
	<!-- Start modal for tøm tabell -->
	<p><input type="button" class="btn btn-md btn-warning" value="Tøm tabell og generer vaktliste" data-toggle="modal" data-target="#modal-generer"></p>
	<div class="modal fade" id="modal-generer" role="dialog">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Vil du tømme tabellen og generere ny vaktliste?</h4>
				</div>
				<div class="modal-body">
					<input type="submit" class="btn btn-md btn-danger" value="Sett i gang" name="generer">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Lukk</button>
				</div>
			</div>
		</div>
	</div>
	<!-- Slutt modal for tøm tabell -->
</div>

</form>

<?php

require_once('bunn.php');

?>
