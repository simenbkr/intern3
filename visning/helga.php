<?php
require_once('topp.php');
$ledige = $max_gjeste_count - $gjeste_count;
$jeg_er_dum = array(0 => 'torsdag', 1 => 'fredag', '2' => 'lordag');
$dag_array = array(
    0 => 'Torsdag',
    1 => 'Fredag',
    2 => 'Lørdag'
);
$side_tittel = "Helga » $dag_array[$dag_tall]";
switch($dag_tall) {
    case 0:
        $undertittel = "[ Torsdag ] [ <a href='?a=helga/fredag'>Fredag</a> ] [ <a href='?a=helga/lordag'>Lørdag</a> ]";
        break;
    case 1:
        $undertittel = "[ <a href='?a=helga/torsdag'>Torsdag</a> ] [ Fredag ] [ <a href='?a=helga/lordag'>Lørdag</a> ]";
        break;
    case 2:
        $undertittel = "[ <a href='?a=helga/torsdag'>Torsdag</a> ] [ <a href='?a=helga/fredag'>Fredag</a> ] [ Lørdag ]";
        break;
    default:
        $undertittel = "[ Torsdag ] [ <a href='?a=helga/fredag'>Fredag</a> ] [ <a href='?a=helga/lordag'>Lørdag</a> ]";
}

?>
<script>
    $("#ajaxform").submit(function(e)
    {
        var postData = $(this).serializeArray();
        var formURL = $(this).attr("action");
        $.ajax(
            {
                url : formURL,
                type: "POST",
                data : postData,
                success:function(html) {
                    $("#container").replaceWith($('#container', $(html)));
                },
                    error: function(jqXHR, textStatus, errorThrown) {}
                });
        e.preventDefault();	//STOP default action
    });
    $("#ajaxform").submit(); //SUBMIT FORM

    function fjern(id, dag) {
        $.ajax({
            type: 'POST',
            url: '?a=helga/<?php echo $jeg_er_dum[$dag_tall];?>',
            data: 'fjern=fjern&gjestid=' + id + "&dag=" + dag,
            method: 'POST',
            success: function (html) {
                document.getElementById(id).remove();
                $('.container').load(document.URL + ' .container');
            },
            error: function (req, stat, err) {
                alert(err);
            }
        });
    }

    function send_epost(id) {
        $.ajax({
            type: 'POST',
            url: '?a=helga/<?php echo $jeg_er_dum[$dag_tall];?>',
            data: 'send=send&gjestid=' + id,
            method: 'POST',
            success: function (html) {
                document.getElementById(id).remove();
                //$('.container').load(document.URL + ' .container');
                $(".container").replaceWith($('.container', $(html)));
            },
            error: function (req, stat, err) {
                alert(err);
            }
        });
    }
</script>
<div class="container">
    <?php
    if (isset($VisError)){
        ?>
        <div class="alert alert-danger fade in" id="success" style="display:table; margin: auto; margin-top: 5%">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            Noe gikk galt. Hva gjorde du?!
        </div>
        <?php
        unset($VisError);
    }
    if (isset($epostSendt)){
        ?>
        <div class="alert alert-success fade in" id="success" style="display:table; margin: auto; margin-top: 5%">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            Epost ble sendt!
        </div>
        <?php
        unset($epostSendt);
    }
    ?>
    <?php require_once ('tilbakemelding.php'); ?>

    <div class="row">
        <h1><?php echo $side_tittel; ?></h1>
        <h3><?php echo $undertittel; ?></h3>
        <p>Her kan du invitere dine venner til Helga!</p>
        <div class="col-lg-6">
            <?php
        for($i = 0; $i < $ledige; $i++){
        ?>
    <div class="formen">
        <form name="ajaxform" id="ajaxform" action="" method="POST">
            <table class="table table-bordered table-responsive">
                        <tr>
                            <input type="hidden" name="add" value="<?php echo $dag_tall; ?>"/>
                            <td>Navn: <input type="text" name="navn" value ="" class="form-control"/></td>
                            <td>Epost: <input type="text" name="epost" value ="" class="form-control"/></td>
                            <td><input class="btn btn-primary" type="submit" value="Legg til"></td>
                        </tr>
            </table>
        </form>
    </div>
                        <?php } ?>
        </div>
        <div class="col-lg-6">

            <table class="table table-bordered table-responsive">
    <?php
    foreach($beboers_gjester as $gjest){
    ?>
        <tr id="<?php echo $gjest->getId(); ?>">
            <td>Navn: <?php echo $gjest->getNavn(); ?></td>
            <td>Epost: <?php echo $gjest->getEpost(); ?></td>
            <td><input class="btn btn-primary" type="submit" value="Slett" onclick="fjern(<?php echo $gjest->getId(); ?>,<?php echo $dag_tall; ?>)"></td>
            <?php if($gjest->getSendt() == 0) { ?>
            <td><input class="btn btn-info" type="submit" value="Send" onclick="send_epost(<?php echo $gjest->getId(); ?>)"></td>
            <?php } else { ?>
                <td><button class="btn btn-info disabled">Send</button></td>
            <?php } ?>
        </tr>
   <?php } ?>

    </div>

</div>

<?php

require_once('bunn.php');

?>
