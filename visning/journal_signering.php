<?php
require_once('topp_journal.php');
require_once('topp.php');
?>
<script>
    function avslutt(id) {
        $.ajax({
            type: 'POST',
            url: '?a=journal/signering/',
            data: "avslutt=1&brukerId=" + id,
            method: 'POST',
            success: function (html) {
                $('body').html(html);
            },
            error: function (req, stat, err) {
                alert(err);
            }
        });
    }
</script>
<div class="container">
    <h1>Journal » Signering</h1>
    <hr>
    <div class="col-lg-6">
        <h2><?php echo $vakta->getFulltNavn(); ?> sitter <?php echo $denne_vakta->getVaktnr(); ?>. vakt nå.
            (<?php echo date('Y-m-d', strtotime($denne_vakta->getDato())); ?>)</h2>

        <div class="tilbakemelding">
            <?php if (isset($_SESSION['success']) && isset($_SESSION['msg'])) { ?>

                <div class="alert alert-success fade in" id="success"
                     style="display:table; margin: auto; margin-top: 5%">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <?php echo $_SESSION['msg']; ?>
                </div>
                <p></p>
                <?php
            } elseif (isset($_SESSION['error']) && isset($_SESSION['msg'])) { ?>
                <div class="alert alert-danger fade in" id="danger" style="display:table; margin: auto; margin-top: 5%">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <?php echo $_SESSION['msg']; ?>
                </div>
                <p></p>
                <?php
            }
            unset($_SESSION['success']);
            unset($_SESSION['error']);
            unset($_SESSION['msg']);
            ?></div>

        <table class="table table-bordered table-responsive">
            <tr>
                <th>Status</th>
                <?php
                $hoved_objektet = $denne_vakta->getStatusAsArray();

                foreach ($hoved_objektet as $obj) {
                    if (($obj['drikkeId'] == 1 || (!$denne_vakta->drukketDenneVakta($obj['drikkeId']) && !\intern3\Drikke::medId($obj['drikkeId'])->getAktiv()))) {
                        continue;
                    }
                    ?>
                    <th><?php echo $drikke_med_id[$obj['drikkeId']]->getNavn(); ?></th>
                <?php } ?>
            </tr>

            <tr>
                <td>Mottatt</td>
                <?php foreach ($hoved_objektet as $obj) {
                    if ($obj['drikkeId'] == 1) {
                        continue;
                    }
                    if ($denne_vakta->drukketDenneVakta($obj['drikkeId']) || \intern3\Drikke::medId($obj['drikkeId'])->getAktiv()) { ?>
                        <td><?php echo $obj['mottatt']; ?></td>

                    <?php }
                }/*
                    if($obj['drikkeId'] == 1 || (!$denne_vakta->drukketDenneVakta($obj['drikkeId']) && !\intern3\Drikke::medId($obj['drikkeId'])->getAktiv())){ continue; }
                    ?>

                        <?php }*/
                ?>
            </tr>

            <tr>
                <td>Påfyll</td>
                <?php foreach ($hoved_objektet as $obj) {
                    if ($obj['drikkeId'] == 1) {
                        continue;
                    }
                    if ($denne_vakta->drukketDenneVakta($obj['drikkeId']) || \intern3\Drikke::medId($obj['drikkeId'])->getAktiv()) { ?>
                        <td><?php echo $obj['pafyll']; ?></td>
                    <?php }
                }?>
            </tr>

            <tr>
                <td>Krysset</td>
                <?php foreach ($hoved_objektet as $obj) {
                    if ($obj['drikkeId'] == 1) {
                        continue;
                    }
                    if ($denne_vakta->drukketDenneVakta($obj['drikkeId']) || \intern3\Drikke::medId($obj['drikkeId'])->getAktiv()) { ?>
                        <td><?php echo $obj['utavskap']; ?></td>
                    <?php }
                }?>
            </tr>

            <tr>
                <td>Avlevert</td>
                <?php foreach ($hoved_objektet as $obj) {
                    if ($obj['drikkeId'] == 1) {
                        continue;
                    }
                    if ($denne_vakta->drukketDenneVakta($obj['drikkeId']) || \intern3\Drikke::medId($obj['drikkeId'])->getAktiv()) { ?>
                        <td><?php echo $obj['avlevert']; ?></td>
                    <?php }
                }?>
            </tr>

        </table>
        <input type="submit" value="Avslutt vakt" onclick="avslutt(<?php echo $vakta->getBrukerId(); ?>)"
               class="btn btn-block btn-warning">
    </div>
</div>
<?php
require_once('bunn.php');
?>
