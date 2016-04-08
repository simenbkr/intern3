<?php

require_once('topp_utvalg.php');

?>

<div class="col-md-12">
	<h1>Utvalget &raquo; Vaktsjef &raquo; Generer vaktliste</h1>
	<p>Dette skjemaet brukes for å lage ny vaktliste. Den gamle vil dermed forsvinne helt. Man oppgir varighet for vaktlista, samt enkeltvakter og perioder hvor man vil tildele manuelt. Deretter vil de resterende vaktene bli fordelt tilfeldig mellom beboerne.</p>
</div>

<div class="col-md-4 col-sm-12">
<h2>Varighet for vaktlista</h2>
  <table class="table-bordered table">
		<tr>
			<th>Fra og med</th>
			<td>
				<select id="varighet_type_start">
					<option value="1">1. vakt</option>
					<option value="2">2. vakt</option>
					<option value="3">3. vakt</option>
					<option value="4">4. vakt</option>
				</select>
				<input class="datepicker" id="varighet_dato_start" size="10" placeholder="dato">
			</td>
		</tr>
		<tr>
			<th>Til og med</th>
			<td>
				<select id="varighet_vakttype_slutt">
					<option value="1">1. vakt</option>
					<option value="2">2. vakt</option>
					<option value="3">3. vakt</option>
					<option value="4">4. vakt</option>
				</select>
				<input class="datepicker" id="varighet_dato_slutt" size="10" placeholder="dato">
			</td>
		</tr>
		<tr>
			<th>Sikkerhetsmargin</th>
			<td colspan="2"><input type="text" id="generer_sikkerhetsmargin" size="2" value="2"><br>Her menes det hvor mange vakter som ikke skal tildeles. De velges tilfeldig.</td>
		</tr>
	</table>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
	<script>
var enkeltvaktIterator = 0;
function flereEnkeltvakter() {
	id = enkeltvaktIterator++;
	$('#enkeltvakter').append('<tr><td><select id="enkeltvakt_type[' + id + ']"><option value="1">1. vakt</option><option value="2">2. vakt</option><option value="3">3. vakt</option><option value="4">4. vakt</option></select> <input class="datepicker" id="enkeltvakt_dato[' + id + ']" size="10" placeholder="dato"></td></tr>');
	formaterDatovelger();
}
$(flereEnkeltvakter);
	</script>
	<h2>Manuelle vakter</h2>
	<p>Her kan man oppgi juleball, helgavakter og andre generelt upassende vakter.</p>
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
	$('#vaktperioder').append('<tr><td><select id="vaktperiode_type_start[' + id + ']"><option value="1">1. vakt</option><option value="2">2. vakt</option><option value="3">3. vakt</option><option value="4">4. vakt</option></select> <input class="datepicker" id="vaktperiode_dato_start[' + id + ']" size="10" placeholder="dato"></td><td><select class="id="vaktperiode_type_slutt[' + id + ']"><option value="1">1. vakt</option><option value="2">2. vakt</option><option value="3">3. vakt</option><option value="4">4. vakt</option></select> <input class="datepicker" id="vaktperiode_dato_slutt[' + id + ']" size="10" placeholder="dato"></td></tr>');
	formaterDatovelger();
}
$(flereVaktperioder);
	</script>
	<h2>Perioder med manuell tildeling</h2>
	<p>Her kan man oppgi eksamenstid, ferier o.l.</p>
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
	<p><input type="button" class="btn btn-md btn-warning" value="Tøm tabell og generer vaktliste" id="tom_tabell_modal" data-toggle="modal" data-target="#modal-tom_tabell"></p>
	<div class="modal fade" id="modal-tom_tabell" role="dialog">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Vil du tømme tabellen og generere ny vaktliste?</h4>
				</div>
				<div class="modal-body">
					<input type="button" class="btn btn-md btn-danger" value="Sett i gang" id="tom_tabell">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Lukk</button>
				</div>
			</div>
		</div>
	</div>
	<!-- Slutt modal for tøm tabell -->
</div>

<?php

require_once('bunn.php');

?>
