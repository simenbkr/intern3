<?php

require_once('topp_utvalg.php');

?>

<div class="col-md-12">
	<h1>Utvalget &raquo; Vaktsjef &raquo; Generer vaktliste</h1>

<!-- Start skript datepicker -->
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<link rel="stylesheet" href="/resources/demos/style.css">
<script>
$(function() {
  $('.datepicker').datepicker({dateFormat: "dd/mm/yy"});
});
</script>
<!-- Slutt skript datepicker -->

<!-- Start generer vaktliste -->
</div>
<div class="col-md-8">
<p><h4>Legg til periode der vaktene skal genereres:</h4></p>
  <table class="table-bordered table">
		<tr>
      <th>Vakttype</th>
			<th>Første vakt</th>
			<th>Siste vakt</th>
			<th>Start eksamensperiode</th>
      <th>Sikkerhetsmargin</th>
		</tr>
    <tr>
      <th>
        <select class="form-control" id="generer_vakttype_start">
          <option value="første">1. vakt</option>
          <option value="andre">2. vakt</option>
          <option value="tredje">3. vakt</option>
          <option value="fjerde">4. vakt</option>
        </select>
      </th>
      <th>
        <div class="form-group">
          <input class="form-control datepicker" id="generer_dato_start" size="3">
        </div>
      </th>
      <th>
        <div class="form-group">
          <input class="form-control datepicker" id="generer_dato_slutt" size="3">
        </div>
      </th>
      <th>
        <div class="form-group">
          <input class="form-control datepicker" id="generer_dato_eksamen" size="3">
        </div>
      </th>
      <th><input type="text" class="form-control" id="generer_sikkerhetsmargin" size="2"></th>
    </tr>
  </table>
  <input type="button" class="btn btn-md btn-info" value="Generer vaktliste" id="generer_vaktliste">

  <!-- Start modal for tøm tabell -->
  <input type="button" class="btn btn-md btn-warning pull-right" value="Tøm tabell" id="tøm_tabell_modal" data-toggle="modal" data-target="#modal-tøm_tabell">
  <div class="modal fade" id="modal-tøm_tabell" role="dialog">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Vil du tømme tabellen?</h4>
        </div>
        <div class="modal-body">
          <input type="button" class="btn btn-md btn-danger" value="Tøm tabell" id="tøm_tabell">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Lukk</button>
        </div>
      </div>
    </div>
  </div>
  <!-- Slutt modal for tøm tabell -->
</div>
<!-- Slutt generer vaktliste -->

<!-- Start vaktperiode -->
<div class="col-md-5">
  <p><h4>Legg til periode der vaktene skal settes manuelt:</h4></p>
  <table class="table-bordered table">
		<tr>
      <th>Vakttype</th>
			<th>Første vakt</th>
      <th>Vakttype</th>
      <th>Siste vakt</th>
		</tr>
    <tr>
      <th>
        <select class="form-control" id="vaktperiode_vakttype_start">
          <option value="første">1. vakt</option>
          <option value="andre">2. vakt</option>
          <option value="tredje">3. vakt</option>
          <option value="fjerde">4. vakt</option>
        </select>
      </th>
      <th>
        <div class="form-group">
          <input class="form-control datepicker" id="vaktperiode_start" size="6">
        </div>
      </th>
      <th>
        <select class="form-control" id="vaktperiode_vakttype_slutt">
          <option value="første">1. vakt</option>
          <option value="andre">2. vakt</option>
          <option value="tredje">3. vakt</option>
          <option value="fjerde">4. vakt</option>
        </select>
      </th>
      <th>
        <div class="form-group">
          <input class="form-control datepicker" id="vaktperiode_slutt" size="6">
        </div>
      </th>
  </table>
  <input type="button" class="btn btn-md btn-info" value="Legg til" id="legg_til_vaktperiode">
<!-- Slutt vaktperiode -->

<!-- Start vakt -->
  <p><h4>Legg til ny vakt:</h4></p>
  <table class="table-bordered table">
		<tr>
      <th>Vakttype</th>
			<th>Første vakt</th>
      <th>Vakttype</th>
      <th>Siste vakt</th>
		</tr>
    <tr>
      <th>
        <select class="form-control" id="vakt_vakttype_start">
          <option value="første">1. vakt</option>
          <option value="andre">2. vakt</option>
          <option value="tredje">3. vakt</option>
          <option value="fjerde">4. vakt</option>
        </select>
      </th>
      <th>
        <div class="form-group">
          <input class="form-control datepicker" id="vakt_start" size="6">
        </div>
      </th>
      <th>
        <select class="form-control" id="vakt_vakttype_slutt">
          <option value="første">1. vakt</option>
          <option value="andre">2. vakt</option>
          <option value="tredje">3. vakt</option>
          <option value="fjerde">4. vakt</option>
        </select>
      </th>
      <th>
        <div class="form-group">
          <input class="form-control datepicker" id="vakt_slutt" size="6">
        </div>
      </th>
  </table>
  <input type="button" class="btn btn-md btn-info" value="Legg til" id="legg_til_vakt">
</div>
<!-- Slutt vakt -->

<?php

require_once('bunn.php');

?>
