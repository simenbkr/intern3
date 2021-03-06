<?php
require_once(__DIR__ . '/../topp_utvalg.php');
?>
<div class="container">

    <h1>Utvalget » Regisjef » Arbeid » Endre Arbeid</h1>

    <form action="" method="post">
        <table class="form table table-responsive table-bordered">
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
                <td><?php
                    $poly = $arbeidet->getPolymorfKategori();
                    if ($poly instanceof \intern3\Oppgave) {
                        echo "Oppgave: ";
                    } elseif ($poly instanceof \intern3\Feil) {
                        echo "Feilretting: ";
                    } elseif ($poly instanceof \intern3\Rapport) {
                        echo "Rapport: ";
                    } elseif ($poly instanceof \intern3\Arbeidskategori) {
                        echo "Arbeid: ";
                    }
                    echo $arbeidet->getPolymorfKategori()->getNavn();

                    ?></td>
            </tr>
            <tr>
                <th>Tid brukt:</th>
                <td><input type="text" name="time" size="6" value="<?php echo $arbeidet->getTidBrukt (); ?>" placeholder="<?php echo $arbeidet->getTidBrukt (); ?>"> (timer på format H:m f.eks 1:20 for 1 time og 20 min.)</td>
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
                <td><input type="hidden" name="endre" value="<?php echo $arbeidet->getId(); ?>"></td>
                <td><input type="submit" class="btn btn-primary" value="Overskriv"></td>
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
    </form>


</div>
<?php

require_once(__DIR__ . '/../../static/bunn.php');

?>