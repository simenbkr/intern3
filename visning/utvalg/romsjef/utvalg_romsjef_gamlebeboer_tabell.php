<?php
require_once(__DIR__ . '/../topp_utvalg.php');

?>
    <div class="col-md-12">
        <h1>Utvalget &raquo; Romsjef &raquo; Gamle beboerliste</h1>
        <table class="table-bordered table">
            <tr>
                <th>Navn</th>
                <th>Telefon</th>
                <th>Epost</th>
                <th>Studie</th>
                <th>Født</th>
                <th>Rolle</th>
            </tr>
            <?php

            foreach ($beboerListe as $beboer){
                ?>
                <tr>
                    <td><a href="?a=utvalg/romsjef/gammelbeboer_tabell/<?php echo $beboer->getId(); ?>"><?php echo $beboer->getFulltNavn(); ?></a></td>
                    <td><?php echo $beboer->getTelefon(); ?></td>
                    <td><a href="mailto:<?php echo $beboer->getEpost(); ?>"><?php echo $beboer->getEpost(); ?></a></td>
                    <td><?php
                        $studie = $beboer->getStudie();
                        $skole = $beboer->getSkole();
                        if ($studie == null || $skole == null) {
                            echo ' ';
                        }
                        else {
                            echo $beboer->getKlassetrinn();
                            ?>. <a href="?a=studie/<?php echo $studie->getId(); ?>"><?php echo $studie->getNavn(); ?></a>&nbsp;(<a href="?a=skole/<?php echo $skole->getId(); ?>"><?php echo $skole->getNavn(); ?></a>)<?php
                        }
                        ?></td>
                    <td><?php echo $beboer->getFodselsdato(); ?></td>
                    <td><?php
                        $utvalgVervListe = $beboer->getUtvalgVervListe();
                        if (count($utvalgVervListe) == 0) {
                            echo str_replace(' ', '&nbsp;', $beboer->getRolle()->getNavn());
                        }
                        else {
                            echo '<strong>' . $utvalgVervListe[0]->getNavn() . '</strong>';
                        }
                        ?></td>
                </tr>
                <?php
            }

            ?>
        </table>
    </div>
<?php
require_once(__DIR__ . '/../../static/bunn.php');
?>