<?php
/* @var \intern3\Vaktbytte $vaktbyttet */
/* @var \intern3\VaktListe $egne_vakter */

?>



<h2>Forslag til <?php echo $vaktbyttet->getVakt()->toString(); ?></h2>
<form action="?a=vakt/bytte/forslag/<?php echo $vaktbyttet->getId(); ?>" method="POST">
    <p>Hvilken vakt vil du foreslå for dette vaktbyttet?</p>

    <table class="table table-bordered">

        <?php foreach($egne_vakter as $vakt){
            /* @var \intern3\Vakt $vakt */
            ?>
        <tr>
            <td><button type="submit" class="btn btn-primary" name="vakt" value="<?php echo $vakt->getId(); ?>"><?php echo $vakt->shortToString(); ?></button></td>
        </tr>

        <?php } ?>


    <?php if($vaktbyttet->harPassord()){ ?>

        <p><input type="password" name="passord" placeholder="Passord" class="form-control"/></p>

    <?php } ?>

</form>