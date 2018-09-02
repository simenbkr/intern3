<?php

require_once(__DIR__ . '/../static/topp.php');

/* @var \intern3\Arbeid $arbeidet */

?>

    <div class="col-md-12">
        <h1>Regi &raquo; Min regi &raquo; Detaljert</h1>
        
        <hr>

            <table class="table table-responsive table-striped">
                <tr>
                    <th>Utført av:</th>
                    <td><?php echo $arbeidet->getBruker()->getPerson()->getFulltNavn(); ?></td>
                </tr>
                <tr>
                    <th>Dato utført:</th>
                    <td><?php echo $arbeidet->getTidUtfort(); ?></td>
                </tr>
                <tr>
                    <th>Dato registrert:</th>
                    <td><?php echo $arbeidet->getTidRegistrert (); ?></td>
                </tr>
                <tr>
                    <th>Kategori:</th>
                    <td><?php echo $arbeidet->getPolymorfKategori()->getNavn(); ?></td>
                </tr>
                <tr>
                    <th>Tid brukt:</th>
                    <td><?php echo $arbeidet->getTidBrukt (); ?></td>
                </tr>
                <tr>
                    <th>Kommentar:</th>
                    <td><?php echo $arbeidet->getKommentar(); ?></td>
                </tr>
                <tr>
                    <th>Status:</th>
                    <td><?php echo $arbeidet->getGodkjent() > 0 ? '<span title="Godkjent ' . substr($arbeidet->getTidGodkjent(), 0, 10) . ' av ' . intern3\Bruker::medId($arbeidet->getGodkjentBrukerId())->getPerson()->getFulltNavn() . '">Godkjent</span>' : 'Ubehandla'; ?></td>
                </tr>

                <tr>
                    <th>Tilbakemelding:</th>
                    <td><?php echo $arbeidet->getTilbakemelding(); ?></td>
                </tr>
                
                <?php
                
                foreach($arbeidet->getArbeidBilder() as $arbeidbilde){
                    /* @var \intern3\ArbeidBilde $arbeidbilde */
                    ?>
                    <tr>
                        <th>Bilde:</th>
                        <td>
                            <a href="<?php echo $arbeidbilde->getPath(); ?>">
                                <img class="img-responsive" src="<?php echo $arbeidbilde->getPath(); ?>">
                            </a>
                        </td>
                        
                    </tr>
                    
                    
                    <?php
                }
                
                
                ?>
                
            </table>
    </div>



<?php

require_once(__DIR__ . '/../static/bunn.php');
