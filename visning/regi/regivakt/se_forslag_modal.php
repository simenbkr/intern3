<?php
/* @var \intern3\Regivaktbytte $bytte */

?>


<h2>Forslag til <?php echo $bytte->getRegivakt()->toString() ?></h2>
<form action="?a=regi/regivakt/godta/<?php echo $bytte->getId(); ?>" method="POST">
    <p>Her er forslagene til bytte for denne vakta:</p>
    <hr>

    <table class="table table-bordered">

        <?php foreach ($bytte->getForslagVakter() as $forslag) {
            /* @var \intern3\RegiBytteForslag $forslag */
            ?>
            <tr>
                <td>
                    <span title="Byttes" class="glyphicon glyphicon-refresh"></span>
                    <button type="submit" class="btn btn-primary" name="rvidbrid"
                            value="<?php echo "{$forslag->getRegivaktId()}:{$forslag->getBrukerId()}"; ?>"><?php echo $forslag->getRegiVakt()->medToString(); ?></button>
                    <br/>
                    <?php echo $forslag->getBruker()->getPerson()->getFulltNavn(); ?>
                </td>
            </tr>

        <?php }

        if (count($bytte->getForslagVakter()) < 1) { ?>
            <p> Det ser ut til at det ikke er noen forslag enda!</p>

        <?php }

        ?>
</form>