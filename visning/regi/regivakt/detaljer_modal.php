<?php

/* @var intern3\Regivakt $rv */

?>

<table class="table table-condensed table-responsive" id="t">
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

<button class="btn btn-danger" onclick="blimed('<?php echo $rv->getId(); ?>')">Bli med</button>

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