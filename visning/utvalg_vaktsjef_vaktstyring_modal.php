<?php
if(isset($_POST['modalId']) && isset($_POST['vakttype']) && isset($_POST['unix'])) {
  $modalId = $_POST['modalId'];
  $vakttype = $_POST['vakttype'];
  $unix = $_POST['unix'];
  if (isset($_POST['vaktId_1'])) {
    $vaktId_1 = $_POST['vaktId_1'];
  }
}
?>
<script>
function settVakt(id) {
  document.getElementById('lagre').style.display = "block";
  var vaktId_1 = '<?php echo $vaktId_1; ?>';
  $.ajax({
    cache: false,
    type: 'POST',
    url: '?a=utvalg/vaktsjef/vaktstyring_settvakt',
    data: 'beboerId=' + id + '&vaktId_1=' + vaktId_1,
    success: function(data) {
      location.reload();
    }
  });
}
function byttVakt(id) {
  if (id == 0) {
    $('#byttvakt').html(' ');
  }
  else {
    var vaktId_1 = '<?php echo $vaktId_1; ?>';
    $.ajax({
      cache: false,
      type: 'POST',
      url: '?a=utvalg/vaktsjef/vaktstyring_byttvakt',
      data: 'beboerId=' + id + '&vaktId_1=' + vaktId_1,
      success: function(data) {
        $('#byttvakt').html(data);
      }
    });
  }
}
function dobbelvakt() {
  var vaktId_1 = '<?php echo $vaktId_1; ?>';
  $.ajax({
    cache: false,
    type: 'POST',
    url: '?a=utvalg/vaktsjef/vaktstyring_dobbelvakt',
    data: 'vaktId_1=' + vaktId_1,
    success: function(data) {
      location.reload();
    }
  });
}
function straffevakt() {
  var vaktId_1 = '<?php echo $vaktId_1; ?>';
  $.ajax({
    cache: false,
    type: 'POST',
    url: '?a=utvalg/vaktsjef/vaktstyring_straffevakt',
    data: 'vaktId_1=' + vaktId_1,
    success: function(data) {
      location.reload();
    }
  });
}
function slettVakt() {
  var vaktId_1 = '<?php echo $vaktId_1; ?>';
  $.ajax({
    cache: false,
    type: 'POST',
    url: '?a=utvalg/vaktsjef/vaktstyring_slettvakt',
    data: 'vaktId_1=' + vaktId_1,
    success: function(data) {
      location.reload();
    }
  });
}
function torildVakt() {
  var vaktId_1 = '<?php echo $vaktId_1; ?>';
  $.ajax({
    cache: false,
    type: 'POST',
    url: '?a=utvalg/vaktsjef/vaktstyring_torildvakt',
    data: 'vaktId_1=' + vaktId_1,
    success: function(data) {
      location.reload();
    }
  });
}

function settBytteMarked(){
  var vaktId = '<?php echo $vaktId_1; ?>';
  $.ajax({
    cache: false,
    type: 'POST',
    url: '?a=utvalg/vaktsjef/vaktstyring_byttemarked',
    data: 'vaktId=' + vaktId,
    success: function(data) {
      location.reload();
    }
  });
}

function test() {
    var shownVal = document.getElementById("tekstinput").value;
    var beboerId = document.querySelector("#beboere option[value='"+shownVal+"']").dataset.value;
    //registrer(gjestid, 1);
    settVakt(beboerId);
    document.getElementById("tekstinput").value = "";
}

function test2() {
    var shownVal = document.getElementById("tekstinput2").value;
    var beboerId = document.querySelector("#beboere option[value='"+shownVal+"']").dataset.value;
    //registrer(gjestid, 1);
    byttVakt(beboerId);
    //document.getElementById("tekstinput2").value = "";
}


</script>
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
        <input type="button" class="btn btn-sm btn-primary" value="Sett vakt" data-toggle="modal" data-target="#<?php echo $modalId; ?>-settvakt">
        <input type="button" class="btn btn-sm btn-primary" value="Bytt vakt" data-toggle="modal" data-target="#<?php echo $modalId; ?>-byttvakt">
        <input type="button" onclick="dobbelvakt()" class="btn btn-sm btn-primary" value="Dobbelvakt">
        <input type="button" onclick="straffevakt()" class="btn btn-sm btn-warning" value="Straffevakt">
        <input type="button" class="btn btn-sm btn-danger" value="Slett vakt" data-toggle="modal" data-target="#<?php echo $modalId; ?>-slettvakt">
        <input type="button" onclick="torildVakt()" class="btn btn-sm btn-info" value="Torild">
        <input type="button" onclick="settBytteMarked()" class="btn btn-sm btn-info" value="Byttemarked">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Lukk</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal for å sette vakter -->
<div class="modal fade" id="<?php echo $modalId; ?>-settvakt" role="dialog">
  <div class="modal-dialog modal-sm">
    <div class="modal-content panel-primary">
      <div class="modal-header panel-heading">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title" align="center"><?php echo $vakttype . '. vakt ' . strftime('%A %d/%m', $unix); ?></h4>
      </div>
      <div class="modal-body" align="center">
        <div class="alert alert-info fade in" id="lagre" style="display: none;">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        Lagret!
        </div>
        <p>Velg hvem som skal ha vakten</p>

          <datalist id="beboere">
              <?php
              foreach ($beboerListe as $beboer) {
                  ?>
                  <option data-value="<?php echo $beboer->getId(); ?>" value="<?php echo $beboer->getFulltNavn(); ?>"></option>
                  <?php
              }
              ?>
          </datalist>
          <input placeholder="Ola Nordmann" id="tekstinput" class="form-control" type="text" list="beboere" onkeydown="if (event.keyCode == 13) { test()}"><br/><br/>

        <select name="beboere" onchange="settVakt(this.value)">
          <option value="0" default="true">- velg -</option>

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
        <!-- <button type="button" onclick="settVakt()" class="btn btn-sm btn-primary" id="lagre">Lagre</button> -->
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

          <datalist id="beboere">
              <?php
              foreach ($beboerListe as $beboer) {
                  ?>
                  <option data-value="<?php echo $beboer->getId(); ?>" value="<?php echo $beboer->getFulltNavn(); ?>"></option>
                  <?php
              }
              ?>
          </datalist>
          <input placeholder="Ola Nordmann" id="tekstinput2" class="form-control" type="text" list="beboere" onkeydown="if (event.keyCode == 13) { test2()}"><br/><br/>


        <select onchange="byttVakt(this.value)">
          <option value="0" default="true">- velg -</option>

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
        <div id="byttvakt">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Lukk</button>
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
        <button type="button" onclick="slettVakt()" class="btn btn-sm btn-warning" data-dismiss="modal">Ja</button>
      </div>
    </div>
  </div>
</div>
