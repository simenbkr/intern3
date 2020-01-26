<?php

/* @var \intern3\Regivakt $rv */

?>
<table class="table table-condensed table-responsive" id="t">
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
        <td></td>
        <td>
            <button class="btn btn-primary"
                    onclick="endre('<?php echo $rv->getId(); ?>')">Endre!
            </button>
        </td>
    </tr>

    <tr>
        <td>Påmeldte:</td>
        <td>
            <?php if (count($rv->getBrukere()) > 0) { ?>
                <ul>
                    <?php foreach ($rv->getBrukere() as $bruker) {
                        /* @var \intern3\Bruker $bruker */

                        if(is_null($bruker)) {
                            continue;
                        }

                        ?>
                        <li id="<?php echo $bruker->getId(); ?>"><?php echo $bruker->getPerson()->getFulltNavn(); ?>
                            <button class="btn btn-sm btn-danger"
                                    onclick="fjern('<?php echo $rv->getId(); ?>','<?php echo $bruker->getId(); ?>')">
                                Fjern
                            </button>
                        </li>
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
            <select class="form-control" onchange="leggTil('<?php echo $rv->getId();?>',this)">Velg
                <option> - </option>
                <?php foreach ($beboere as $beboer) {

                    if(in_array($beboer->getBrukerId(), $rv->getBrukerIder())) {
                        continue;
                    }

                    ?>
                    <option value="<?php echo $beboer->getBrukerId(); ?>"><?php echo $beboer->getFulltNavn(); ?></option>
                <?php } ?>

            </select>
        </td>
    </tr>

</table>

<script>

    function leggTil(rvid, elem) {
        var brid = elem.options[elem.selectedIndex].value;

        $.ajax({
            type: 'POST',
            url: '?a=utvalg/regisjef/regivakt/add_bruker',
            data: 'rvid=' + rvid + '&brid=' + brid,
            method: 'POST',
            success: function (html) {
                $("#mod").load("?a=utvalg/regisjef/regivakt/administrer/" + rvid);
            },
            error: function (req, stat, err) {
                alert(err);
            }
        });
    }

    function endre(rvid) {
        console.log(rvid);

        var dato = document.getElementById('dato-modalen').value;
        var start = document.getElementById('start-modalen').value;
        var slutt = document.getElementById('slutt-modalen').value;
        var antall = document.getElementById('antall-modalen').value;
        var beskrivelse = document.getElementById('beskrivelse-modalen').value;
        var nokkelord = document.getElementById('nokkelord-modalen').value;

        $.ajax({
            type: 'POST',
            url: '?a=utvalg/regisjef/regivakt/endre',
            data: 'rvid=' + rvid + '&dato=' + dato + '&start=' + start + '&slutt=' + slutt + '&beskrivelse=' + beskrivelse + '&nokkelord=' +
                nokkelord + '&antall=' + antall,
            method: 'POST',
            success: function (html) {
                $('#modal-modal').modal('toggle');
                location.reload();
            },
            error: function (req, stat, err) {
                alert(err);
            }
        });

    }

    $(function () {
        $('#dato-modalen').datetimepicker({
            format: 'YYYY-MM-DD'
        });
    });

    $(function () {
        $('#start-modalen').datetimepicker({
            format: 'HH:ss'
        });
    });

    $(function () {
        $('#slutt-modalen').datetimepicker({
            format: 'HH:ss'
        });
    });


    function fjern(rvid, bid) {
        $.ajax({
            type: 'POST',
            url: '?a=utvalg/regisjef/regivakt/fjern',
            data: 'rvid=' + rvid + '&brid=' + bid,
            method: 'POST',
            success: function (html) {
                document.getElementById(bid).remove();
            },
            error: function (req, stat, err) {
                alert(err);
            }
        });

    }
</script>