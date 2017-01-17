<?php
require_once ('topp_utvalg.php');
?>
<script>
    function endreTimer(id){
        var timer = document.getElementById(id).value;
        $.ajax({
            type: 'POST',
            url: '?a=utvalg/sekretar/apmandstimer',
            data: 'endreTimer=1&timer=' + timer + "&vervId=" + id,
            method: 'POST',
            success: function (data) {
                $("#" + id).replaceWith($('#' + id, $(data)));
            },
            error: function (req, stat, err) {
                alert(err);
            }
        });
    }
</script>
<div class="container">
<h1>Utvalget &raquo; Sekretær &raquo; Endre Åpmandstimer</h1>
    <hr>
    <table class="table table-bordered">
        <tr>
            <th>Åpmandsverv</th>
            <th>Åpmand/åpmend</th>
            <th>Epost</th>
            <th>Endre</th>
        </tr>
        <?php
        foreach ($apmandsverv as $verv) {
            ?>
            <div class="<?php echo $verv->getId();?>">
                <tr>
                    <td><?php echo $verv->getNavn(); ?></td>
                    <td><?php
                        $i = 0;
                        foreach ($verv->getApmend() as $apmand) {
                            if ($i++ > 0) {
                                echo ', ';
                            }
                            echo '<a href="?a=beboer/' . $apmand->getId() . '">' . $apmand->getFulltNavn() . '</a>';?>
                        <?php } ?>
                    </td><td><?php
                        $epost = $verv->getEpost();
                        if ($verv == null) {
                            echo ' ';
                        } else {
                            echo '<a href="mailto:' . $epost . '">' . $epost . '</a>';
                        }
                        ?></td>
                    <td><input type="text" name="regitimer" placeholder="<?php echo $verv->getRegitimer();?>" id="<?php echo $verv->getId();?>">
                        <button class="btn btn-sm btn-primary" onclick="endreTimer(<?php echo $verv->getId();?>)">Endre</button></td>
            <?php
        }
        ?>
        </tr>

</div>
<?php
require_once ('bunn.php');
?>