<?php

require_once('topp.php');

?>

<div class="col-md-12">
	<h1>Utvalget &raquo; Kosesjef &raquo; Utleie</h1>

<?php

require_once('utvalgmeny.php');

?>
  <p> </p>
  <h2>Legg til utleie:</h2>

  <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
  <script src="//code.jquery.com/jquery-1.10.2.js"></script>
  <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
  <link rel="stylesheet" href="/resources/demos/style.css">
  <script>
  $(function() {
    $( "#datepicker" ).datepicker({dateFormat: "dd/mm/yy"});
  });
  </script>
</div>
<div class="col-md-7">
  <table class="table-bordered table">
		<tr>
			<th>Dato</th>
			<th>Rom</th>
			<th>Leietaker</th>
		</tr>
    <tr>
      <th>
        <div class="form-group">
          <input class="form-control" id="datepicker" size="3">
        </div>
      </th>
			<th>
        <select class="form-control">
          <option value="Bodegaen">Bodegaen</option>
          <option value="Salongen">Salongen</option>
        </select>
      </th>
			<th><input type="text" class="form-control"></th>
    </tr>
  </table>
  <input type="button" class="btn btn-sm btn-info" value="Legg til">
</div>

<?php

require_once('bunn.php');

?>
