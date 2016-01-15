<?php

require_once('topp.php');

?>

<div class="col-md-12">
	<h1>Utvalget &raquo; Sekretær &raquo; Utvalgsverv</h1>

<?php

require_once('utvalgmeny.php');

?>

  <p> </p>
  <h2>Endre utvalgsverv:</h2>

</div>

<div class="col-md-6">
  <table class="table-bordered table">
    <tr>
      <th>Utvalgsverv</th>
      <th>Beboer</th>
    </tr>
    <tr>
      <td><select class="form-control">

<?php
foreach ($vervListe as $verv) {
?>

  <option value="<?php echo $verv->getId(); ?>">
  <?php echo $verv->getNavn(); ?>
  </option>

<?php
}
?>
    </td>
    <td><select class="form-control">

<?php
foreach ($beboerListe as $beboer) {
?>

<option value="<?php echo $beboer->getId(); ?>">
<?php echo $beboer->getFulltNavn(); ?>
</option>

<?php
}
?>
  </select>
  </td>
  </tr>
</table>

<input type="button" class="btn btn-sm btn-info" value="Endre">

</div>

<?php

require_once('bunn.php');

?>
