<?php
require_once('topp_journal.php');
require_once(__DIR__ . '/../static/topp.php');

?>
<script>
    function bytte(brukerId) {
        $.ajax({
            type: 'POST',
            url: '?a=journal/vaktbytte',
            data: 'brukerId=' + brukerId,
            method: 'POST',
            success: function (data) {
                history.back();
            },
            error: function (req, stat, err) {
                alert(err);
            }
        });
    }
</script>
<div class="container">
    <h1>Journal » Vaktbytte</h1>
    <hr>
    </br>
    <div class="row">
        <div class="col-lg-12 text-center">
            <?php
            foreach ($aktuelle as $beboer){ ?>
            <h2>
                <input class="btn btn-default btn-block" type="submit" value="<?php echo $beboer->getFulltNavn(); ?>"
                       onclick="bytte(<?php echo $beboer->getBrukerId(); ?>)"><br/>
                <?php
                }
                ?>
                <hr>
                <a href="javascript:history.back()">TILBAKE</a>
        </div>
    </div>
</div>


    <?php
    require_once(__DIR__ . '/../static/bunn.php');

    ?>
