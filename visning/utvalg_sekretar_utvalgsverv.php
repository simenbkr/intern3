<?php

require_once('topp_utvalg.php');

?>
<script>
function fjern(beboerId,vervId) {
  $.ajax({
    type: 'POST',
    url: '?a=utvalg/sekretar/utvalgsverv',
    data: 'fjern=' + beboerId +'&verv='+ vervId,
    method: 'POST',
    success: function (data) {
      location.reload();
    },
    error: function (req, stat, err) {
      alert(err);
    }
  });
}
</script>
<div class="col-md-12">
    <h1>Utvalget &raquo; Sekretær &raquo; Utvalgsverv</h1>


    <p></p>
    <h2>Endre utvalgsverv:</h2>

</div>

<div class="col-md-6">
    <form action="" method="post">
    <table class="table-bordered table">
        <tr>
            <th>Utvalgsverv</th>
            <th>Beboer</th>
        </tr>
        <tr>
            <td>
              <select name="vervid" id="vervid">
                <option value="0">- velg -</option>
                    <?php
                    foreach ($vervListe as $verv) {
                        ?>

                        <option name="<?php echo $verv->getNavn(); ?>" value="<?php echo $verv->getId(); ?>">
                            <?php echo $verv->getNavn(); ?>
                        </option>

                        <?php
                    }
                    ?>
              </select>
            </td>
            <td>
              <select name="beboerid" id="beboerid">
                <option value="0">- velg -</option>
                    <?php
                    foreach ($beboerListe as $beboer) {
                        ?>

                        <option name="beboerid" value="<?php echo $beboer->getId(); ?>">
                            <?php echo $beboer->getFulltNavn(); ?>
                        </option>

                        <?php
                    }
                    ?>
              </select>
            </td>
        </tr>
        <tr>
        <td></td>
        <td><input type="submit" class="btn btn-sm btn-info" value="Legg til" name="legg til"></td>
        </tr>
    </table>
    </form>

  <table class="table-bordered table">
    <tr>
      <th>Utvalgsverv</th>
      <th>Beboer</th>
    </tr>

<?php foreach ($vervListe as $verv) {
?>

      <tr>
        <td><?php echo $verv->getNavn(); ?></td>
        <td>
          <?php
          if ($verv->getApmend() != null) {
            foreach ($verv->getApmend() as $apmand) {
              echo $apmand->getFulltNavn();
              ?>
              <button onclick="fjern(<?php echo $apmand->getId(); ?>,<?php echo $verv->getId(); ?>)">&#x2718;</button>
              <?php
            }
          } else { echo ' '; }
          ?>
        </td>
      </tr>
<?php
}
?>
    </tr>
  </table>
</div>


<?php

require_once('bunn.php');

?>
