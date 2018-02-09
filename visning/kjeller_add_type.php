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
        <p>[ <a href="<?php echo $cd->getBase(); ?>kjeller/admin">Vinadministrasjon</a> ]
            [ <a href="<?php echo $cd->getBase(); ?>kjeller/slettet_vin">Slettet vin</a> ]
            [ <a href="<?php echo $cd->getBase(); ?>kjeller/leggtil">Legg til vin</a> ]
            [ Vintyper ]
            [ <a href="<?php echo $cd->getBase(); ?>kjeller/pafyll">Påfyll</a> ]
            [ <a href="<?php echo $cd->getBase(); ?>kjeller/lister">Lister</a> ]
            [ <a href="<?php echo $cd->getBase(); ?>kjeller/regning">Regning</a> ]
            [ <a href="<?php echo $cd->getBase(); ?>kjeller/svinn">Svinn</a> ]
            [ <a href="<?php echo $cd->getBase(); ?>kjeller/lister/beboere_vin">Fakturer</a> ]
            [ <a href="<?php echo $cd->getBase(); ?>kjeller/oversikt">Oversikt</a> ]
        </p>
    <hr>
    <?php
        require_once ('tilbakemelding.php');
    ?>

        <div class="col-md-12">
            <form action="" method="post" enctype="multipart/form-data">
                <table class="table table-bordered table-responsive">
                    <tr>
                        <td>Navn:</td>
                        <td><input type="text" name="navn" class="form-control" value=""></td>
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
                        <td><a href="?a=kjeller/add_type/<?php echo $vintypen->getId();?>"><?php echo $vintypen->getNavn();?></a></td>
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