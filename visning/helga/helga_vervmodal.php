<?php
if(isset($_POST['vervId']) && isset($_POST['vervNavn'])) {
    $vervId = $_POST['vervId'];
    $vervNavn = $_POST['vervNavn'];
}
?>
<!-- Modal for åpmandsverv -->
<div class="modal fade" id="<?php echo $vervId; ?>-åpmand" role="dialog">
    <div class="modal-dialog modal-sm">
        <div class="modal-content panel-primary">
            <div class="modal-header panel-heading">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" align="center"><?php echo $vervNavn; ?></h4>
            </div>
            <div class="modal-body" align="center">
                <p>Velg hvem som skal ha åpmandsvervet</p>
                <form action="" method="POST">
                    <select id="vervet" name="vervet" onchange="this.form.submit()">
                        <option value="0">- velg -</option>
                        <?php
                        foreach (intern3\BeboerListe::aktive() as $beboer) {
                            ?>
                            <option id="vervet" value="<?php echo $beboer->getId() . '&' . $vervId; ?>" name="<?php echo $beboer->getId() . '&' . $vervId; ?>">
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
