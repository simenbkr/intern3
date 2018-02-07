<?php
/* @var \intern3\Vaktbytte $vaktbyttet */
/* @var \intern3\VaktListe $egne_vakter */

?>



<h2>Forslag til <?php echo $vaktbyttet->getVakt()->toString(); ?></h2>
<form action="?a=vakt/bytte/forslag/<?php echo $vaktbyttet->getId(); ?>" method="POST" id="formen">
    <p>Hvilken vakt vil du foreslå for dette vaktbyttet?</p>

    <table class="table table-bordered">

        <?php foreach($egne_vakter as $vakt){
            /* @var \intern3\Vakt $vakt */
            ?>
        <tr>
            <td>
                <button type="submit" class="btn btn-primary" name="vakt" value="<?php echo $vakt->getId(); ?>"><?php echo $vakt->shortToString(); ?></button>

                <?php
                if(in_array($vakt->getId(), $vaktbyttet->getForslagIder())){ ?>

                    <button type="submit" class="btn btn-danger" name="vakt"
                            onclick="submitForm('?a=vakt/bytte/slettbytte/<?php echo $vaktbyttet->getId(); ?>')"
                            value="<?php echo $vakt->getId(); ?>">X</button>

                <?php
                }

                ?>

            </td>
        </tr>

        <?php } ?>


    <?php if($vaktbyttet->harPassord()){ ?>

        <p><input type="password" name="passord" placeholder="Passord" class="form-control"/></p>

    <?php } ?>

</form>

<script>
    function submitForm(action) {
        console.log("sup");
        document.getElementById('formen').action = action;
        document.getElementById('formen').submit();
    }
</script>