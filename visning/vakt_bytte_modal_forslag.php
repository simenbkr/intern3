<?php
/* @var \intern3\Vaktbytte $vaktbyttet */

?>



<h2>Forslag til <?php echo $vaktbyttet->getVakt()->toString(); ?></h2>
<form action="?a=vakt/bytte/bytte/<?php echo $vaktbyttet->getId(); ?>" method="POST">
    <p>Her er forslagene til bytte for denne vakta:</p>

    <table class="table table-bordered">

        <?php foreach($vaktbyttet->getForslagVakter() as $vakt){
            /* @var \intern3\Vakt $vakt */
            ?>
            <tr>
                <td>
                    <span title="Byttes" class="glyphicon glyphicon-refresh"></span>
                    <button type="submit" class="btn btn-primary" name="vakt" value="<?php echo $vakt->getId(); ?>"><?php echo $vakt->shortToString(); ?></button>
                    <br/>
                    <?php echo $vakt->getBruker()->getPerson()->getFulltNavn(); ?>
                </td>
            </tr>

        <?php }

        if (count($vaktbyttet->getForslagVakter()) < 1){ ?>
            <p> Det ser ut til at det ikke er noen forslag enda!<p/>

        <?php }

        ?>

</form>