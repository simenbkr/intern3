<?php
/* @var \intern3\Vaktbytte $vaktbyttet */

?>
<form action="?a=vakt/bytte/slett/<?php echo $vaktbyttet->getId(); ?>" method="POST">
    <h3>Vil du slette vaktbyttet?</h3>
    <button class="btn btn-danger">Slett vaktbytte!</button>
</form>