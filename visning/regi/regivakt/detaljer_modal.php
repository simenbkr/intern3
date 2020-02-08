<?php

/* @var intern3\Regivakt $rv */

?>

<table class="table table-condensed table-responsive" id="t">
    <tr>
        <td>Status:</td>
        <td><?php echo $rv->getStatus(); ?></td>
    </tr>
    <tr>
        <td>Dato:</td>
        <td><?php echo $rv->getDato(); ?></td>
    </tr>
    <tr>
        <td>Start:</td>
        <td>
            <?php echo $rv->getStartTid(); ?>
        </td>
    </tr>
    <tr>
        <td>Slutt:</td>
        <td>
            <?php echo $rv->getSluttTid(); ?>
        </td>
    </tr>
    <tr>
        <td>Påmeldte</td>
        <td><?php echo "{$rv->getAntallPameldte()}/{$rv->getAntall()}"; ?></td>
    </tr>
    <tr>
        <td>Beskrivelse:</td>
        <td>
            <?php echo $rv->getBeskrivelse(); ?>
        </td>
    </tr>

    <tr>
        <td>Påmeldte:</td>
        <td>
            <?php $out = '';
                foreach($rv->getBrukere() as $bruker) {
                    $out .= "{$bruker->getPerson()->getFulltNavn()}, ";
                }
                echo rtrim($out, ', ');
            ?>
        </td>
    </tr>

</table>

<?php
if($rv->getStatusInt() == 0 && $rv->harPlass() && !in_array($aktiv_bruker->getId(), $rv->getBrukerIder())) { ?>

<button class="btn btn-primary" onclick="blimed('<?php echo $rv->getId(); ?>')">Bli med</button>

<?php } ?>

<script>
    function blimed(id) {
        $.ajax({
            method: 'POST',
            type: 'POST',
            url: '?a=regi/regivakt/blimed',
            data: 'id=' + id,
            success: function (data) {
                location.reload();
            }
        });
    }
</script>