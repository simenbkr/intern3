<?php
require_once('static/topp.php');
?>
<script>
    function meldPa(utleieid, felt) {
        $.ajax({
            type: 'POST',
            url: '?a=utleie',
            data: 'meldpa=1' + '&utleieid=' + utleieid + "&felt=" + felt,
            method: 'POST',
            success: function (html) {
                $(".container").replaceWith($('.container', $(html)));
            },
            error: function (req, stat, err) {
                alert(err);
            }
        });
    }
    function meldAv(utleieid) {
        $.ajax({
            type: 'POST',
            url: '?a=utleie',
            data: 'meldpa=0' + '&utleieid=' + utleieid,
            method: 'POST',
            success: function (html) {
                $(".container").replaceWith($('.container', $(html)));
            },
            error: function (req, stat, err) {
                alert(err);
            }
        });
    }
</script>
<div class="container">
    <?php if (isset($success)) {
        ?>
        <div class="alert alert-success fade in" id="success" style="display:table; margin: auto; margin-top: 5%">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            Du meldte deg på som barvakt/vaskevakt!
        </div>
        <?php
        unset($success);
    }
    if (isset($avmeldt)) { ?>
        <div class="alert alert-success fade in" id="success" style="display:table; margin: auto; margin-top: 5%">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            Du meldte deg av som barvakt/vaskevakt!
        </div>
        <?php
    } ?>
    <div class="col-md-12">
        <h1>Utleie » Påmelding</h1>
        <p>Her kan du melde deg på som barvakt/vaskehjelp.</p>
        <table class="table-bordered table">
            <tr>
                <th>Dato</th>
                <th>Rom</th>
                <th>Leietaker</th>
                <th>Barvakt 1</th>
                <th>Barvakt 2</th>
                <th>Vasking</th>
            </tr>

            <?php
            foreach ($utleier as $utleiet) {
                ?>
                <tr>
                    <td><?php echo $utleiet->getDato(); ?></td>
                    <td><?php echo $utleiet->getRom(); ?></td>
                    <td><?php echo $utleiet->getNavn(); ?></td>
                    <td><?php echo $utleiet->getBeboer1() != null ? $utleiet->getBeboer1()->getFulltNavn() : ($utleiet->erBeboerPameldt($aktuell_beboer) ? '' : "<input class=\"btn btn-primary\" type=\"submit\" value=\"Meld på\" onclick=\"meldPa(" . $utleiet->getId() . ",1)\""); ?></td>
                    <td><?php echo $utleiet->getBeboer2() != null ? $utleiet->getBeboer2()->getFulltNavn() : ($utleiet->erBeboerPameldt($aktuell_beboer) ? '' : "<input class=\"btn btn-primary\" type=\"submit\" value=\"Meld på\" onclick=\"meldPa(" . $utleiet->getId() . ",2)\""); ?></td>
                    <td><?php echo $utleiet->getBeboer3() != null ? $utleiet->getBeboer3()->getFulltNavn() : ($utleiet->erBeboerPameldt($aktuell_beboer) ? '' : "<input class=\"btn btn-primary\" type=\"submit\" value=\"Meld på\" onclick=\"meldPa(" . $utleiet->getId() . ",3)\""); ?></td>
                    <td><?php echo $utleiet->erBeboerPameldt($aktuell_beboer) ? "<input class=\"btn btn-primary\" type=\"submit\" value=\"Meld av\" onclick=\"meldAv(" . $utleiet->getId() . ")\"" : ''; ?></td>
                </tr>
                <?php
            }
            ?>
        </table>
    </div>
</div>
<?php
require_once('static/bunn.php');
?>
