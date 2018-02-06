<?php
/* @var \intern3\Vaktbytte $vaktbyttet */
/* @var \intern3\Vakt $vakta */

?>
<form action="?a=vakt/bytte/ta/<?php echo $vaktbyttet->getId(); ?>" method="POST">
    <h3>Vil du ta vakta <?php echo $vaktbyttet->getVakt()->toString();?>?</h3>
    <button class="btn btn-warning">Ja!</button>
</form>