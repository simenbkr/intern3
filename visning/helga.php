<?php

require_once('topp.php');



$ledige = $max_gjeste_count - $gjeste_count;
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

    function fjern(id) {
        $.ajax({
            type: 'POST',
            url: '?a=helga',
            data: 'fjern=fjern&gjestid=' + id,
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
            url: '?a=helga',
            data: 'send=send&gjestid=' + id,
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
    if (isset($epostError)){
        ?>
        <div class="alert alert-danger fade in" id="success" style="display:table; margin: auto; margin-top: 5%">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            Ikke gyldig epost!
        </div>
        <?php
        unset($epostError);
    }
    ?>
    <div class="row">
        <h1>Helga!</h1>
        <p>Her kan du invitere dine venner til Helga!</p>
        <div class="col-lg-6">



            <?php
        for($i = 0; $i < $ledige; $i++){
        ?>
    <div class="formen">
        <form name="ajaxform" id="ajaxform" action="" method="POST">
            <table class="table table-bordered table-responsive">
                        <tr>
                            <input type="hidden" name="add" value="add"/>
                            <td>Navn: <input type="text" name="navn" value =""/></td>
                            <td>Epost: <input type="text" name="epost" value =""/></td>
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
            <td><input class="btn btn-primary" type="submit" value="Slett" onclick="fjern(<?php echo $gjest->getId(); ?>)"></td>
            <?php if($gjest->getSendt() == 0) { ?>
            <td><button class="btn btn-info" onclick="send_epost(<?php echo $gjest->getId(); ?>">Send</button></td>
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
