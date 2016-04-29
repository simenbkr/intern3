<?php

require_once('topp_utvalg.php');

?>

<script>
function velgBeboer(id) {
  $.get('?a=utvalg/romsjef/endrebeboer_tabell/'+id, function(data) {
    $('#endrebeboer').html(data);
  });
}
</script>

<div class="col-md-4">
	<h1>Utvalget &raquo; Romsjef &raquo; Endre Beboer</h1>

  <p><select onchange="velgBeboer(this.value)">
    <option value="0">- velg -</option>
<?php
foreach ($beboerListe as $beboer) {
?>    <option value="<?php echo $beboer->getId(); ?>"><?php echo $beboer->getFulltNavn(); ?></option>
<?php
}
?>

  </select></p>

  <div id="endrebeboer">
  </div>

</div>

<?php

require_once('bunn.php');

?>
