<style>
    .table-responsiver {
        max-height:350px;
        overflow-y:scroll;
    }
</style>

<script>

    var knappen = document.getElementById('knapp');

    $('#collapse1').on('show.bs.collapse', function() {
        knappen.onclick = function () {
            document.getElementById('form1').submit();
        }
    });

    $('#collapse1').on('hidden.bs.collapse', function() {
        knappen.onclick = function(){};
    })

    $('#collapse2').on('show.bs.collapse', function() {
        knappen.onclick = function () {
            document.getElementById('form2').submit();
        }
    });

    $('#collapse2').on('hidden.bs.collapse', function() {
        knappen.onclick = function(){};
    })

</script>

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
</div>

<button class="btn btn-default btn-block" id="knapp">Velg</button>