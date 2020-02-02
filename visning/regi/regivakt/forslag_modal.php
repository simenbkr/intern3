<?php
/* @var \intern3\Regivaktbytte $bytte */

?>

<h2>Forslag til <?php echo $bytte->getRegivakt()->toString(); ?></h2>
<form action="?a=regi/regivakt/forslag/<?php echo $bytte->getId(); ?>" method="POST" id="formen">
    <p>Hvilken regivakt vil du foreslå for dette regivaktbyttet?</p>

    <table class="table table-bordered">

        <?php if(count($egne_regivakter) == 0) { ?>
            <b>Du har ingen regivakter du kan bytte mot!</b>
        <?php
        }

            foreach ($egne_regivakter as $rv) {
            /* @var \intern3\Regivakt $rv */
            ?>
            <tr>
                <td>

                    <?php
                    if (in_array($rv->getId(), $bytte->getForslagIder())) { ?>

                        <button disabled="disabled"
                                class="btn btn-primary disabled"><?php echo $rv->medToString(); ?></button>

                        <p>
                            <button type="submit" class="btn btn-danger" name="rvid"
                                    onclick="submitForm('?a=regi/regivakt/slett_forslag/<?php echo $bytte->getId(); ?>')"
                                    value="<?php echo $rv->getId(); ?>">Trekk
                            </button>
                        </p>

                        <?php
                    } else { ?>

                        <button type="submit" class="btn btn-primary" name="rvid"
                                value="<?php echo $rv->getId(); ?>"><?php echo $rv->medToString(); ?></button>

                    <?php } ?>

                </td>
            </tr>

        <?php } ?>


        <?php if ($bytte->harPassord()) { ?>

            <p><input type="password" name="passord" placeholder="Passord" class="form-control"/></p>

        <?php } ?>

</form>

<script>
    function submitForm(action) {
        document.getElementById('formen').action = action;
        document.getElementById('formen').submit();
    }
</script>