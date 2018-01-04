<?php
/* @var \intern3\Verv $vervet */

require_once('topp_utvalg.php');

?>

    <div class="container">
        <div class="col-lg-12">
            <h1>Utvalg » Åpmandsverv » Endre <?php echo $vervet->getNavn(); ?></h1>
            <hr>
            <?php require_once('tilbakemelding.php'); ?>
            <form action="" method="POST" onsubmit="">
                <table class="table table-bordered table-responsive">
                    <tr>
                        <td>Navn:</td>
                        <td><input class="form-control" type="text" name="navn"
                                   value="<?php echo $vervet->getNavn(); ?>"></td>
                    </tr>
                    <tr>
                        <td>Beskrivelse:</td>
                        <td><textarea class="form-control" rows="10" cols="50"
                                      name="beskrivelse"><?php echo $vervet->getBeskrivelse(); ?></textarea></td>
                    </tr>
                    <tr>
                        <td>Åpmend</td>
                        <td><?php
                            $str = "";
                            foreach ($vervet->getApmend() as $beboer) {
                                if ($beboer != null) {
                                    $str .= '<a href="?a=beboer/' . $beboer->getId() . '">' . $beboer->getFulltNavn() .
                                        '</a>, <button onclick="fjern(' . $beboer->getId() . ',' . $vervet->getId() . ')">&#x2718;</button>';
                                }
                            }
                            echo rtrim($str, ', '); ?></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><input class="btn btn-primary" type="submit" value="Endre" name="endre"></td>
                    </tr>

                    <?php if (count($vervet->getApmend()) < 1) { ?>
                        <tr>
                            <td></td>
                            <td><input class="btn btn-danger" type="submit" value="Slett vervet"
                                       onclick="slett(<?php echo $vervet->getId(); ?>)"></td>
                        </tr>

                    <?php } else { ?>
                        <tr>
                            <td></td>
                            <td>
                                <p><input type="button" class="btn btn-md btn-danger" value="Slett vervet"
                                          data-toggle="modal" data-target="#modal-nullstill"></p>
                                <div class="modal fade" id="modal-nullstill" role="dialog">
                                    <div class="modal-dialog modal-sm">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal">&times;
                                                </button>
                                                <h4 class="modal-title">Er du sikker på at du vil
                                                    slette <?php echo $vervet->getNavn(); ?>?</h4>
                                                <p>Det har for øyeblikket <?php echo count($vervet->getApmend()); ?>
                                                    åpmend. Slettingen kan <b>IKKE</b> reverseres.</p>
                                            </div>
                                            <div class="modal-body">
                                                <input class="btn btn-md btn-danger" type="submit"
                                                       onclick="slett(<?php echo $vervet->getId(); ?>)" value="SLETT"/>
                                                <div id="kult" style="display:none">
                                                    <p>
                                                        Oki. Sletter nå..
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">
                                                    Lukk
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </td>
                        </tr>


                    <?php } ?>

                </table>
            </form>
        </div>
    </div>

    <script>
        function fjern(beboerId, vervId) {
            $.ajax({
                type: 'POST',
                url: '?a=utvalg/sekretar/apmandsverv',
                data: 'fjern=' + beboerId + '&verv=' + vervId,
                method: 'POST',
                success: function (data) {
                    window.location.reload();
                },
                error: function (req, stat, err) {
                    alert(err);
                }
            });
        }
    </script>

    <script>
        function slett(id) {
            $.ajax({
                type: 'POST',
                url: '?a=utvalg/sekretar/apmandsverv/' + id,
                data: 'slett=' + id,
                method: 'POST',
                success: function (data) {
                    location.reload();
                },
                error: function (req, stat, err) {
                    alert(err);
                }
            });
        }
    </script>


<?php

require_once('bunn.php');

?>