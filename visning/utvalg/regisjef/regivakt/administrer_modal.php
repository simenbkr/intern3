<?php

/* @var \intern3\Regivakt $rv */

?>

<table class="table table-condensed table-responsive">
    <tr>
        <td>Dato:</td>
        <td>
            <input id="dato-modalen" type="text" name="dato" value="<?php echo $rv->getDato(); ?>"
                   class="form-control"/></td>
    </tr>
    <tr>
        <td>Start:</td>
        <td>
            <input id="start-modalen" type="text" name="start-tid" value="<?php echo $rv->getStartTid(); ?>"
                   class="form-control"/></td>
    </tr>
    <tr>
        <td>Slutt:</td>
        <td>
            <input id="slutt-modalen" type="text" name="slutt-tid" value="<?php echo $rv->getSluttTid(); ?>"
                   class="form-control"/></td>
    </tr>
    <tr>
        <td>Maks Antall:</td>
        <td><input id="antall-modalen" type="number" name="antall" value="<?php echo $rv->getAntall(); ?>"
                   class="form-control"/></td>
    </tr>
    <tr>
        <td>Beskrivelse:</td>
        <td>
            <textarea id="beskrivelse-modalen" name="beskrivelse" cols="50" rows="5" class="form-control"
            ><?php echo $rv->getBeskrivelse(); ?></textarea>

    </tr>
    <tr>
        <td>Nøkkelord</td>

        <td>
            <input id="nokkelord-modalen" type="text" name="nokkelord" value="<?php echo $rv->getNokkelord(); ?>"
                   class="form-control"/></td>
    </tr>

    <tr>
        <td>Påmeldte:</td>
        <td>
            <?php if (count($rv->getBrukere()) > 0) { ?>
                <ul>
                    <?php foreach ($rv->getBrukere() as $bruker) {
                        /* @var \intern3\Bruker $bruker */
                        ?>
                        <li><?php echo $bruker->getPerson()->getFulltNavn(); ?></li>
                    <?php } ?>
                </ul>
            <?php } else { ?>
                Ingen påmeldte!
            <?php }
              ?>
        </td>
    </tr>

    <tr>
        <td>Meld på:</td>
        <td>
            <select class="form-control">Velg
            <option> - </option>

            </select>
        </td>
    </tr>

</table>
