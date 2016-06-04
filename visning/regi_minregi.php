<?php

require_once('topp.php');

?>

<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<link rel="stylesheet" href="/resources/demos/style.css">

<script>
$(function() {
  $( "#datepicker" ).datepicker({dateFormat: "dd/mm/yy"});
});
</script>

<div class="col-sm-3 col-xs-12">
  <h1>Regi &raquo; Min Regi</h1>
  <form>
    <table class="table table-bordered table-responsive">
      <tr>
        <th>Kategori:</th>
        <td>
          <select>
          <option>Diverse</option>
          </select>
        </td>
      </tr>
      <tr>
        <th>Dato utført:</th>
        <td><input id="datepicker"></td>
      </tr>
      <tr>
        <th>Tid brukt:</th>
        <td><input type="text" id="time" name="time" placeholder="0:00" required=""></td>
      </tr>
      <tr>
        <th>Kommentar:</th>
        <td><textarea id="kommentar" name="kommentar" rows="3"></textarea></td>
      </tr>
      <tr>
        <td></td>
        <td><input type="submit" class="btn btn-md btn-primary" value="Register" name="register"></td>
      </tr>
    </table>
  </form>
</div>

<?php

require_once('bunn.php');

?>
