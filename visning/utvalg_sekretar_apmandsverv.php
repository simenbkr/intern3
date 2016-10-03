<?php

require_once('topp_utvalg.php');

?>
<script>
function fjern(beboerId,vervId) {
  $.ajax({
    type: 'POST',
    url: '?a=utvalg/sekretar/apmandsverv',
    data: 'fjern=' + beboerId +'&verv='+ vervId,
    method: 'POST',
    success: function (data) {
      window.location.reload();
    },
    error: function (req, stat, err) {
      alert(err);
    }
  });
}
function modal() {
  var vervId = $(this).attr('data-target');
  var vervNavn = $(this).attr('data-name');
  $.ajax({
    cache: false,
    type: 'POST',
    url: '?a=utvalg/sekretar/apmandsverv_modal',
    data: 'vervId=' + vervId + '&vervNavn=' + vervNavn,
    success: function(data) {
      $('#modal-' + vervId).html(data);
      $('#' + vervId + '-åpmand').modal('show');
      $('#' + VervId + '-åpmand').on('hidden.bs.modal', function() {
        $('#modal-' + vervId).html(' ');
      });
    }
  });
}
</script>
<div class="col-md-12">
    <h1>Utvalget &raquo; Sekretær &raquo; Åpmandsverv</h1>


    <p></p>

</div>

<div class="col-md-12">
    <table class="table table-bordered">
        <tr>
            <th>Åpmandsverv</th>
            <th>Åpmand/åpmend</th>
            <th>Legg til/endre</th>
            <th>Epost</th>
        </tr>
        <?php

        foreach ($vervListe as $verv) {
        ?>
        <div id="<?php echo $verv->getId();?>">
        <tr id="<?php echo $verv->getId();?>">
            <td><?php echo $verv->getNavn(); ?></td>
            <td><?php
                $i = 0;
                foreach ($verv->getApmend() as $apmand) {
                    if ($i++ > 0) {
                        echo ', ';
                    }
                    echo '<a href="?a=beboer/' . $apmand->getId() . '">' . $apmand->getFulltNavn() . '</a>';?> <button onclick="fjern(<?php echo $apmand->getId(); ?>,<?php echo $verv->getId(); ?>)">&#x2718;</button>
                <?php } ?>
                </td>
            <td></div>
              <div>
                <input type="button" class="btn btn-sm btn-info" value="Legg Til" onclick="modal.call(this)" data-target="<?php echo $verv->getId(); ?>" data-name="<?php echo $verv->getNavn(); ?>">
                <div id="modal-<?php echo $verv->getId(); ?>">
                </div>
              </div>
            </td>
            <td><?php
                $epost = $verv->getEpost();
                if ($verv == null) {
                    echo ' ';
                } else {
                    echo '<a href="mailto:' . $epost . '">' . $epost . '</a>';
                }
                ?></td>
            <?php
            }
            ?>
        </tr>


        <!-- <input type="button" class="btn btn-sm btn-info" value="Endre"> -->

</div>

</table>
<?php

require_once('bunn.php');

?>
