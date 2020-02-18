<?php
/* @var \intern3\Regivaktbytte $bytte */
?>

<form action="?a=regi/regivakt/gisbort/<?php echo $bytte->getId(); ?>" method="POST">
    <h3>Vil du ta regivakta <?php echo $bytte->getRegivakt()->medToString();?>?</h3>

    <?php if($bytte->harPassord()){ ?>

        <p><input type="password" name="passord" placeholder="Passord" class="form-control"/></p>

    <?php } ?>

    <button class="btn btn-warning">Ja!</button>
</form>