<?php
/* @var \intern3\Verv $vervet */

require_once(__DIR__ . '/../topp_utvalg.php');

$checked = $vervet->erUtvalg() ? 'checked=checked' : '';

?>

    <div class="container">
        <div class="col-lg-12">
            <h1>Utvalg » Åpmandsverv » Endre <?php echo $vervet->getNavn(); ?></h1>
            <hr>
            <?php include(__DIR__ . '/../../static/tilbakemelding.php'); ?>
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
                        <td>Regitimer:</td>
                        <td><input type="number" name="regitimer" class="form-control"
                                   value="<?php echo $vervet->getRegitimer(); ?>"></td>
                    </tr>

                    <tr>
                        <td>E-post:</td>
                        <td><input type="text" name="epost" class="form-control"
                                   value="<?php echo $vervet->getEpost(); ?>"></td>
                    </tr>
                    <tr>
                        <td>Utvalg?</td>
                        <td><input type="checkbox" name="utvalg" value="1" <?php echo $checked; ?>> <br></td>
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
                            echo rtrim($str, ', '); ?>

                            <div>
                                <input type="button" class="btn btn-sm btn-info" value="Legg Til" onclick="modal.call(this)"
                                       data-target="<?php echo $vervet->getId(); ?>" data-name="<?php echo $vervet->getNavn(); ?>">
                                <div id="modal-<?php echo $vervet->getId(); ?>">
                                </div>
                            </div>

                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><input class="btn btn-primary" type="submit" value="Endre" name="endre"></td>
                    </tr>
                </table>
            </form>

            <table>
            <?php if (count($vervet->getApmend()) < 1) { ?>
                <form action="" method="POST" onsubmit="">
                    <table>
                    <input type="hidden" name="slett" value="<?php echo $vervet->getid(); ?>">
                    <tr>
                        <td></td>
                        <td><input class="btn btn-danger" type="submit" value="Slett vervet"
                                   name="<?php echo $vervet->getId(); ?>"></td>
                    </tr>
                    </table>
                </form>

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

                                        <form action="" method="POST" onsubmit="">
                                            <table>
                                                <input type="hidden" name="slett" value="<?php echo $vervet->getid(); ?>">
                                                <tr>
                                                    <td></td>
                                                    <td><input class="btn btn-danger" type="submit" value="Slett vervet"
                                                               name="<?php echo $vervet->getId(); ?>"></td>
                                                </tr>
                                            </table>
                                        </form>
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
        function modal() {
            var vervId = $(this).attr('data-target');
            var vervNavn = $(this).attr('data-name');
            $.ajax({
                cache: false,
                type: 'POST',
                url: '?a=utvalg/sekretar/apmandsverv_modal',
                data: 'vervId=' + vervId + '&vervNavn=' + vervNavn,
                success: function (data) {
                    $('#modal-' + vervId).html(data);
                    $('#' + vervId + '-åpmand').modal('show');
                    $('#' + VervId + '-åpmand').on('hidden.bs.modal', function () {
                        $('#modal-' + vervId).html(' ');
                    });
                }
            });
        }
    </script>


<?php

require_once(__DIR__ . '/../../static/bunn.php');

?>