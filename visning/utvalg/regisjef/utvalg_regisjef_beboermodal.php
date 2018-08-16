<div class="modal fade" id="beboerlista" role="dialog">
    <div class="modal-dialog modal-sm">
        <div class="modal-content panel-primary">
            <div class="modal-header panel-heading">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" align="center">Beboerliste</h4>
            </div>
            <div class="modal-body" align="center">
                <p>Velg beboer</p>
                <form action="" method="POST">
                    <select id="vervet" name="beboer" onchange="this.form.submit()">
                        <option value="0">- velg -</option>
                        <?php
                        foreach ($beboerListe as $beboer) {
                            ?>
                            <option id="beboeren" value="<?php echo $beboer->getId(); ?>" name="beboer">
                                <?php echo $beboer->getFulltNavn(); ?>
                            </option>
                            <?php
                        }
                        ?>
                    </select>
                    <noscript><input type="submit" value="Submit"></noscript>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Lukk</button>
            </div>
        </div>
    </div>
</div>
