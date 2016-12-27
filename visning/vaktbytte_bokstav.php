<?php
require_once('topp.php');
?>
<script>
    function bytte(beboerId) {
        $.ajax({
            type: 'POST',
            url: '?a=journal/vaktbytte',
            data: 'beboerId=' + beboerId,
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
    <div class="row">
<?php
foreach($aktuelle as $beboer){ ?>
<h2>
    <input class="btn btn-default btn-block" type="submit" value="<?php echo $beboer->getFulltNavn(); ?>" onclick="bytte(<?php echo $beboer->getId(); ?>)"><br/>
</h2>
        <?php
}
?>
</div>
    <a href="javascript:history.back()">TILBAKE</a>
</div>



<?php
require_once('bunn.php');
?>
