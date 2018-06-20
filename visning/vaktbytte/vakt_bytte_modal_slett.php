<?php
/* @var \intern3\Vaktbytte $vaktbyttet */

?>
<form action="?a=vakt/bytte/slett/<?php echo $vaktbyttet->getId(); ?>" method="POST">
    <h3>Vil du fjerne vakta <?php echo $vaktbyttet->getVakt()->toString(); ?> fra byttemarkedet?</h3>
    <?php if(!$vaktbyttet->getGisBort()) { ?>
    <p>Dette vil slette alle forslag, og kan ikke omgjøres.</p>
    <?php } ?>
    <hr>
    <button class="btn btn-danger">Slett vaktbytte!</button>
</form>