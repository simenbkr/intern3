<style>
    .table-responsiver {
        max-height:350px;
        overflow-y:scroll;
    }
</style>

<script>

    var knappen = document.getElementById('knapp');

    $('#collapse1').on('shown.bs.collapse', function() {
        knappen.onclick = function () {
            document.getElementById('form1').submit();
        }
    });

    $('#collapse1').on('hide.bs.collapse', function() {
        knappen.onclick = function(){};
    })

    $('#collapse2').on('shown.bs.collapse', function() {
        knappen.onclick = function () {
            document.getElementById('form2').submit();
        }
    });

    $('#collapse2').on('hide.bs.collapse', function() {
        knappen.onclick = function(){};
    })

    function setSingel() {
        mode = 'singel';
        var lista = document.getElementsByClassName('mode');
        for(var i = 0; i < lista.length; i++) {
            var item = lista[i];
            item.value = 'singel';
        }
    }

    function setDobbel(){
        mode = 'dobbel';
        var lista = document.getElementsByClassName('mode');
        for(var i = 0; i < lista.length; i++) {
            var item = lista[i];
            item.value = 'dobbel';
        }
    }

</script>

<p>Velg modus. Fler gjør at alle du velger blir én og samme velger. Singel legger til alle valgte som individuelle velgere.</p>
<div class="btn-group" data-toggle="buttons">
    <label class="btn btn-primary active">
        <input type="radio" onchange="setSingel()" name="options" id="option1" checked> Singel
    </label>
    <label class="btn btn-primary">
        <input type="radio" onchange="setDobbel()" name="options" id="option2"> Fler
    </label>
</div>
<hr>
<div class="panel-group" id="accordion">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion" href="#collapse1">
                    Ikke på Storhybellista</a>
            </h4>
        </div>
        <div id="collapse1" class="panel-collapse collapse table-responsiver">
            <div class="panel-body">

                <form id="form1" action="?a=utvalg/romsjef/storhybel/liste/leggtilvelger/<?php echo $lista->getId(); ?>"
                      method='post'>
                    <input type="hidden" name="mode" class="mode" value="singel">
                    <table class="table table-condensed">
                        <thead>
                        <tr>
                            <th>Navn</th>
                            <th>Velg</th>
                        </tr>
                        </thead>

                        <tbody>
                        <?php
                        foreach ($ikke_reg_beboerliste as $beboer) {
                            /* @var \intern3\Beboer $beboer */
                            ?>
                            <tr>
                                <td><?php echo $beboer->getFulltNavn(); ?></td>
                                <td><input name='beboere[]' type='checkbox'
                                           value="<?php echo $beboer->getId(); ?>"/></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </form>


            </div>
        </div>
    </div>
<?php if(!$bare_manglende) { ?>
    <div class="panel panel-default" style="height:100px overflow:auto">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion" href="#collapse2">
                    Alle</a>
            </h4>
        </div>
        <div id="collapse2" class="panel-collapse collapse table-responsiver">
            <div class="panel-body">

                <form id="form2" action="?a=utvalg/romsjef/storhybel/liste/leggtilvelger/<?php echo $lista->getId(); ?>"
                      method='post'>
                    <input type="hidden" name="mode" class="mode" value="singel">
                    <table class="table table-condensed">
                        <thead>
                        <tr>
                            <th>Navn</th>
                            <th>Velg</th>
                        </tr>
                        </thead>

                        <tbody>
                        <?php
                        foreach ($beboerliste_alle as $beboer) {
                            /* @var \intern3\Beboer $beboer */
                            ?>
                            <tr>
                                <td><?php echo $beboer->getFulltNavn(); ?></td>
                                <td><label><input name='beboere[]' type='checkbox'
                                                  value="<?php echo $beboer->getId(); ?>"/></label></td>
                            </tr>
                        <?php } ?>

                        </tbody>
                    </table>

                </form>


            </div>
        </div>
    </div>
    <?php } ?>
</div>

<button class="btn btn-default btn-block" id="knapp">Velg</button>