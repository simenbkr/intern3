<?php
require_once('topp.php');
?>
<script>
    function slett(id) {
        $.ajax({
            type: 'POST',
            url: '?a=kjeller/add_type',
            data: 'slett=' + id,
            method: 'POST',
            success: function (html) {
                $(".container").replaceWith($('.container', $(html)));
                //$('#oppgave_' + id).html(data);
                //location.reload();
            },
            error: function (req, stat, err) {
                alert(err);
            }
        });
    }
</script>
    <div class="container">
    <h1>Kjellermester » Legg til vintype</h1>
    <hr>
    <?php if (isset($error)){ ?>
    <div class="alert alert-danger fade in" id="success" style="display:table; margin: auto; margin-top: 5%">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        Noe gikk galt! Vintypen ble ikke lagt til!
        <?php }
        unset($error) ?>

        <div class="col-md-12">
            <form action="" method="post" enctype="multipart/form-data">
                <table class="table table-bordered table-responsive">
                    <tr>
                        <td>Navn:</td>
                        <td><input type="text" name="navn" value=""></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><input class="btn btn-primary" type="submit" value="Legg til"></td>
                    </tr>
                </table>
            </form>
        </div>
        <div class="col-lg-6">
            <table class="table table-bordered table-responsive">
                <tr>
                    <th>Navn</th>
                    <th>Slett</th>
                </tr>
                <?php foreach ($vintyper as $vintypen) {
                    ?>
                    <tr>
                        <td><?php echo $vintypen->getNavn();?></td>
                        <td><button class="btn btn-danger btn-sm" onclick="slett(<?php echo $vintypen->getId();?>)">Slett</button></td>
                    </tr>
                    <?php
                }
                ?>
            </table>
        </div>
    </div>
<?php
require_once('bunn.php');
?>