<?php
/* @var \intern3\Vaktbytte $vaktbyttet */

?>
<form action="?a=vakt/bytte/slett/<?php echo $vaktbyttet->getId(); ?>" method="POST">
    <h3>Vil du slette vaktbyttet for <?php echo $vaktbyttet->getVakt()->toString(); ?>?</h3>
    <p>Dette vil slette alle forslag, og kan ikke omgjøres.</p>
    <hr>
    <button class="btn btn-danger">Slett vaktbytte!</button>
</form>