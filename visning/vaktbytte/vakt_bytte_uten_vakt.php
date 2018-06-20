<?php

require_once(__DIR__ . '/../static/topp.php');


/* @var \intern3\Beboer $beboer */
/* @var \intern3\VaktListe $egne_vakter */


?>

<div class="container">

    <div class="col-md-12">
        <h1>Vakt » Vaktbytte</h1>
        <p>[ <a href="?a=vakt">Vakt</a> ] [ Vaktbytte ]</p>
    
        <?php include(__DIR__ . '/../static/tilbakemelding.php'); ?>
        
    </div>

    <div class="col-md-3 col-sm-6 col-sx-12">
        <p class="tekst_dinevakter">Du skal ikke sitte vakter, og har ingen vakter å bytte.</p>
        
    </div>

    <div class="col-md-12">
        
        <?php
        foreach (range(1, 4) as $type) { ?>

            <div class="col-md-3 col-sm-6 col-sx-12">
                <table class="table table-bordered">
                    <tr>
                        <th><?php echo $type; ?>. vakt</th>
                    </tr>
                    
                    <?php foreach ($vaktbytter as $vaktbytte) {
                        /* @var \intern3\Vaktbytte $vaktbytte */
                        
                        if ($vaktbytte->getVakt()->getVakttype() != $type) {
                            continue;
                        }
                        
                        ?>

                        <tr>
                            <td>
                                
                                <?php
                                
                                if ($vaktbytte->harPassord()) { ?>
                                    <span title="Passordlåst" class="glyphicon glyphicon-lock"></span>
                                <?php }
                                if ($vaktbytte->getGisBort()) { ?>
                                    <span title="Gis bort" class="glyphicon glyphicon-alert"></span>
                                    <?php
                                } else { ?>
                                    <span title="Byttes" class="glyphicon glyphicon-refresh"></span>
                                <?php }
                                
                                if ($vaktbytte->getVakt()->getBrukerId() === $beboer->getBrukerId()) { ?>
                                    <button class="btn-sm btn-primary pull-right disabled" disabled="disabled">
                                        Se forslag</button>>
                                    <button data-target="#myModal" data-remote="false" class="btn-sm btn-danger pull-right disabled" disabled="disabled">
                                        Trekk</button>>
                                <?php } else {
                                    if (!$vaktbytte->getGisBort()) { ?>
                                        <button class="btn-sm btn-info pull-right disabled">Bytt</button>
                                        <?php
                                    } else { ?>
                                        <button class="btn-sm btn-warning pull-right disabled">Ta vakt</button>
                                        <?php
                                    }
                                    
                                }
                                
                                echo $vaktbytte->getVakt()->shortToString();
                                echo "<br/>";
                                echo $vaktbytte->getVakt()->getBruker()->getPerson()->getFulltNavn();
                                
                                if($vaktbytte->getMerknad() != null && strlen($vaktbytte->getMerknad()) > 1){
                                    echo "<br/>";
                                    echo $vaktbytte->getMerknad();
                                }
                                
                                ?>

                            </td>
                        </tr>
                    <?php }
                      ?>
                </table>
            </div>
        <?php }
        ?>
    </div>
</div>

<?php

require_once(__DIR__ . '/../static/bunn.php');

?>