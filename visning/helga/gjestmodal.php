<?php
/* @var \intern3\HelgaGjesteObjekt $gjest */
?>

<script>
    function endre(i) {
        var navn = document.getElementById('navn-' + i).value;
        var epost = document.getElementById('epost-' + i).value;

        var days = [];
        ['torsdag-' + i, 'fredag-' + i, 'lordag-' + i].forEach(function (str, index) {
            if (document.getElementById(str).checked) {
                days.push(str.split('-')[0]);
                $('#' + str.split('-')[0] + '-' + '<?php echo $gjest->getIder()[0]; ?>').text('✗')
            } else {
                console.log('#' + str.split('-')[0] + '-' + '<?php echo $gjest->getIder()[0]; ?>');
                $('#' + str.split('-')[0] + '-' + '<?php echo $gjest->getIder()[0]; ?>').text('');
            }
        });
        var d = days.join(';');

        if (navn.length > 1 && epost.length > 2 && epost.includes('@') && days.length > 0) {
            $.ajax({
                type: 'POST',
                url: '?a=helga/endre',
                data: 'id=' + '<?php echo $gjest->getIder()[0]; ?>' + '&navn=' + navn + '&epost=' + epost + '&dager=' + d,
                method: 'POST',
                success: function (html) {
                    $('#gjest-modal').modal('toggle');
                    //$('#lista').load(document.URL + ' #lista');


                },
                error: function (req, stat, err) {
                    alert(err);
                }
            });
        } else {
            tilbakemelding("Ugyldig epost eller ingen dager valgt!");
        }
    }

</script>


<table class="table table-condensed table-responsive">
    <tr id="add-modalen">
        <td>Navn:
            <input id="navn-modalen" type="text" name="navn" value="<?php echo $gjest->getNavn(); ?>"
                   class="form-control"/></td>
        <td>Epost:
            <input id="epost-modalen" type="text" name="epost" value="<?php echo $gjest->getEpost(); ?>"
                   class="form-control"/></td>
        <td>
            Torsdag <input id="torsdag-modalen"
                           class="checkbox-inline" <?php echo $gjest->gjestTorsdag() ? 'checked=checked' : ''; ?>
                           type="checkbox" name="torsdag"
                           value="torsdag"/>
        </td>
        <td>
            Fredag <input id="fredag-modalen" class="checkbox-inline"
                          type="checkbox" <?php echo $gjest->gjestFredag() ? 'checked=checked' : ''; ?>
                          name="fredag" value="fredag"/>
        </td>
        <td>
            Lørdag <input id="lordag-modalen" class="checkbox-inline"
                          type="checkbox" <?php echo $gjest->gjestLordag() ? 'checked=checked' : ''; ?>
                          name="lordag" value="lordag"/>
        </td>
    </tr>

</table>

<button class="btn btn-primary"
        onclick="endre('modalen')">Endre
</button>