<?php

require_once('topp.php');

$gjester_max = 15;

$ledige = $gjester_max - $gjeste_count;

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
                success:function(html){
                    $("#formen").replaceWith($('#formen', $(html)));
                    {

                    },
                    error: function(jqXHR, textStatus, errorThrown)
                    {
                    }
                });
        e.preventDefault();	//STOP default action
    });

    $("#ajaxform").submit(); //SUBMIT FORM
</script>
<div class="container">
    <h1>Helga!</h1>
    <p>Her kan du invitere dine venner til Helga!</p>


            <?php
        for($i = 0; $i < $ledige; $i++){
        ?>
    <div class="formen">
        <form name="ajaxform" id="ajaxform" action="" method="POST">
            <table class="table table-bordered table-responsive">
                        <tr>
                            <td>Navn</td>
                            <td><input type="text" name="navn" value =""/></td>
                            <td>Epost:</td>
                            <td><input type="text" name="epost" value =""/></td>
                            <td><input class="btn btn-primary" type="submit" value="Legg til"></td>
                        </tr>
            </table>
        </form>
    </div>
                        <?php } ?>

    <div id="formen">
        <form name="ajaxform" id="ajaxform" action="" method="POST">
            <table class="table table-bordered table-responsive">
    <?php
    foreach($beboers_gjester as $gjest){
    ?>
        <tr>
            <input type
            <td>Navn</td>
            <td><input type="text" name="navn" value ="<?php echo $gjest->getNavn(); ?>"/></td>
            <td>Epost:</td>
            <td><input type="text" name="epost" value ="<?php echo $gjest->getEpost(); ?>"/></td>
            <td><input class="btn btn-primary" type="submit" value="Slett"></td>
        </tr>
   <?php } ?>



</div>

<?php

require_once('bunn.php');

?>
