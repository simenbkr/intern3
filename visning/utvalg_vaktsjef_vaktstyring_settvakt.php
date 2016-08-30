<select>
  <option value="0">- velg -</option>

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
