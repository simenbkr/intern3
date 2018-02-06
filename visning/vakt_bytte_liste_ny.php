<?php

require_once ('topp.php');

/* @var \intern3\Beboer $beboer */
/* @var \intern3\VaktListe $egne_vakter */



?>

<div class="container">


    <div class="col-md-12">
        <h1>Vakt » Vaktbytte</h1>
        <p>[ <a href="?a=vakt">Vakt</a> ] [ Vaktbytte ]</p>
        
        <?php
            require_once ('tilbakemelding.php');
        ?>
    </div>
    
    <div class="col-md-3 col-sm-6 col-sx-12">
    <table class="table table-bordered">
        
        <tr>
            <th>Dine vakter</th>
        </tr>
        
    <?php foreach($egne_vakter as $vakt) {
        /* @var \intern3\Vakt $vakt */
        ?>
        <tr>
            <?php
            
            if($vakt->erFerdig() || $vakt->erStraffevakt()) {
                //Vakter brukere ikke kan gjøre noe med.
                ?>
                
                <td class="celle_graa"> <?php echo $vakt->toString(); ?></td>
                
                <?php
            } else {
                ?>
                <td><a href="?a=vakt/bytte/modal_egen/<?php echo $vakt->getId();?>" data-toggle="modal"
                       data-target="#myModal" data-remote="false" class="btn btn-primary"><?php echo $vakt->toString() ;?></a></td>
                <?php
            }
            ?>
        </tr>
        
    <?php } ?>
    </table>
    </div>
</div>

<div class="col-md-12">

    <?php
        foreach(range(1,4) as $type){ ?>
    
            <div class="col-md-3 col-sm-6 col-sx-12">
                <table class="table table-bordered">
                    <tr>
                        <th><?php echo $type;?>. vakt</th>
                    </tr>
                    
                    <?php foreach($vaktbytter as $vaktbytte){ ?>
                    
                    
                    
                    <?php } ?>
                </table>
            </div>
    <?php }
    ?>
    
    
    
    
</div>





<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Legg ut vakt</h4>
            </div>
            <div class="modal-body">
                ...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
    // Fill modal with content from link href
    $("#myModal").on("show.bs.modal", function(e) {
        var link = $(e.relatedTarget);
        $(this).find(".modal-body").load(link.attr("href"));
    });
</script>
<?php

require_once ('bunn.php');

?>
