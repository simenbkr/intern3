<script>

    function leggTil() {

        var dato = document.getElementById('dato-modalen').value;
        var start = document.getElementById('start-modalen').value;
        var slutt = document.getElementById('slutt-modalen').value;
        var antall = document.getElementById('antall-modalen').value;
        var beskrivelse = document.getElementById('beskrivelse-modalen').value;
        var nokkelord = document.getElementById('nokkelord-modalen').value;

        $.ajax({
            type: 'POST',
            url: '?a=utvalg/regisjef/regivakt/add',
            data: 'dato=' + dato + '&start=' + start + '&slutt=' + slutt + '&beskrivelse=' + beskrivelse + '&nokkelord=' +
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

</script>


<table class="table table-condensed table-responsive">
    <tr>
        <td>Dato:</td>
        <td>
            <input id="dato-modalen" type="text" name="dato" value="<?php echo $dato; ?>"
                   class="form-control"/></td>
    </tr>
    <tr>
        <td>Start:</td>
        <td>
            <input id="start-modalen" type="text" name="start-tid" value="" placeholder="Starttidspunkt på dagen."
                   class="form-control"/></td>
    </tr>
    <tr>
        <td>Slutt:</td>
        <td>
            <input id="slutt-modalen" type="text" name="slutt-tid" value="" placeholder="Slutttidspunkt på dagen."
                   class="form-control"/></td>
    </tr>
    <tr>
        <td>Antall:</td>
        <td><input id="antall-modalen" type="number" name="antall" placeholder="Antall personer på denne vakta"
                   class="form-control"/></td>
    </tr>
    <tr>
        <td>Beskrivelse:</td>
        <td>
            <textarea id="beskrivelse-modalen" name="beskrivelse" cols="50" rows="5" class="form-control"
                      placeholder="Lenger beskrivelse av oppgaven."></textarea>

    </tr>
    <tr>
        <td>Nøkkelord</td>

        <td>
            <input id="nokkelord-modalen" type="text" name="nokkelord" value=""
                   placeholder="1-4 ords beskrivelse av oppgaven til intern bruk."
                   class="form-control"/></td>
    </tr>
</table>


<button class="btn btn-primary"
        onclick="leggTil()">Legg til!
</button>