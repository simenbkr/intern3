<script>
function velgBeboer(id) {
  if (id == 0) {
    $('#settvakt').html('Feil oppstod');
  }
  else {
    $.get('?a=utvalg/vaktsjef/vaktstyring_settvakt/'+id, function(data) {
      $('#settvakt').html(data);
    });
  }
}
</script>
<?php
if(isset($_POST['modalId']) && isset($_POST['vakttype'])) {
  $modalId = $_POST['modalId'];
  $vakttype = $_POST['vakttype'];
  $unix = $_POST['unix'];
}
?>
<!-- Modal for vakter -->
<div class="modal fade" id="<?php echo $modalId; ?>" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content panel-primary">
      <div class="modal-header panel-heading">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title" align="center"><?php echo $vakttype . '. vakt ' . strftime('%A %d/%m', $unix); ?></h4>
      </div>
      <div class="modal-body" align="center">
        <p> </p>
        <input type="button" class="btn btn-sm btn-primary" value="Bytt vakt" data-toggle="modal" data-target="#<?php echo $modalId; ?>-byttvakt">
        <input type="button" class="btn btn-sm btn-primary" value="Dobbelvakt" data-target="#<?php echo $modalId; ?>-dobbelvakt">
        <input type="button" class="btn btn-sm btn-warning" value="Straffevakt" data-target="#<?php echo $modalId; ?>-straffevakt">
        <input type="button" class="btn btn-sm btn-danger" value="Slett vakt" data-toggle="modal" data-target="#<?php echo $modalId; ?>-slettvakt">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Lukk</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal for ledige vakter -->
<div class="modal fade" id="<?php echo $modalId; ?>-ledig" role="dialog">
  <div class="modal-dialog modal-sm">
    <div class="modal-content panel-primary">
      <div class="modal-header panel-heading">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title" align="center"><?php echo $vakttype . '. vakt ' . strftime('%A %d/%m', $unix); ?></h4>
      </div>
      <div class="modal-body" align="center">
        <!-- <div class="alert alert-info fade in" id="lagret" style="display: none;">Lagret!<button type="button" class="close" onclick="hideLagret()">&times;</button>
        </div> -->
        <p>Velg hvem som skal ha vakten</p>
        <select onchange="velgBeboer(this.value)">
          <option value="0">- velg -</option>

          <?php
          foreach ($beboerListe as $beboer) { // Denne velger kun de som har vakt
          ?>

          <option value="<?php echo $beboer->getId(); ?>">
          <?php echo $beboer->getFulltNavn(); ?>
          </option>

          <?php
          }
          ?>
        </select>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Lukk</button>
        <button type="button" class="btn btn-sm btn-primary" id="lagre">Lagre</button> <!-- TODO må fikses! -->
      </div>
    </div>
  </div>
</div>
<!-- Modal for å slette vakter -->
<div class="modal fade" id="<?php echo $modalId; ?>-slettvakt" role="dialog">
  <div class="modal-dialog modal-sm">
    <div class="modal-content panel-primary">
      <div class="modal-header panel-heading">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title" align="center"><?php echo $vakttype . '. vakt ' . strftime('%A %d/%m', $unix); ?></h4>
      </div>
      <div class="modal-body" align="center">
        <p>Er du sikker på at du vil slette denne vakten?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Nei</button>
        <button type="button" class="btn btn-sm btn-primary" data-target="DOSOMETHING!" data-dismiss="modal">Ja</button> <!-- TODO må fikses! (DOSOMETHING!) -->
      </div>
    </div>
  </div>
</div>
<!-- Modal for å bytte vakt -->
<div class="modal fade" id="<?php echo $modalId; ?>-byttvakt" role="dialog">
  <div class="modal-dialog modal-sm">
    <div class="modal-content panel-primary">
      <div class="modal-header panel-heading">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title" align="center"><?php echo $vakttype . '. vakt ' . strftime('%A %d/%m', $unix); ?></h4>
      </div>
      <div class="modal-body" align="center">
        <p>Velg hvem som skal ha vakten</p>
        <select onchange="velgBeboer(this.value)">
          <option value="0">- velg -</option>

          <?php
          foreach ($beboerListe as $beboer) { // Denne velger kun de som har vakt
          ?>

          <option value="<?php echo $beboer->getId(); ?>">
          <?php echo $beboer->getFulltNavn(); ?>
          </option>

          <?php
          }
          ?>
        </select>
        <div id="settvakt">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Lukk</button>
      </div>
    </div>
  </div>
</div>
