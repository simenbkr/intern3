<?php

require_once('topp.php');

?>

<div class="col-md-4">
    <h1>Beboer &raquo; <?php echo $beboer->getFulltNavn(); ?></h1>
    <p>[ <a href="<?php echo $cd->getBase(); ?>beboer">Beboerliste</a> ] [ <a
            href="<?php echo $cd->getBase(); ?>beboer/utskrift">Utskriftsvennlig</a> ] [ <a
            href="<?php echo $cd->getBase(); ?>beboer/statistikk">Statistikk</a> ]
        [ <a href="<?php echo $cd->getBase(); ?>beboer/kart">Beboerkart</a> ]
        [ <a href="<?php echo $cd->getBase();?>beboer/gamle">Gamle Beboere</a> ]
    </p>
    <table class="table table-bordered">
        <tr>
            <th>Rom</th>
            <td><?php echo $beboer->getRom()->getNavn(); ?></td>
        </tr>
        <tr>
            <th>Telefon</th>
            <td><?php echo $beboer->getTelefon(); ?></td>
        </tr>
        <tr>
            <th>Epost</th>
            <td><a href="mailto:<?php echo $beboer->getEpost(); ?>"><?php echo $beboer->getEpost(); ?></a></td>
        </tr>
        <tr>
            <th>Studie</th>
            <td><?php
                $studie = $beboer->getStudie();
                $skole = $beboer->getSkole();
                if ($studie == null || $skole == null) {
                    echo ' ';
                } else {
                    echo $beboer->getKlassetrinn();
                    ?>. <a
                        href="<?php echo $cd->getBase(); ?>studie/<?php echo $studie->getId(); ?>"><?php echo $studie->getNavn(); ?></a> (
                    <a href="<?php echo $cd->getBase(); ?>skole/<?php echo $skole->getId(); ?>"><?php echo $skole->getNavn(); ?></a>)<?php
                }
                ?></td>
        </tr>
        <tr>
            <th>Født</th>
            <td><?php echo $beboer->getFodselsdato(); ?></td>
        </tr>
        <tr>
            <th>Rolle</th>
            <td><?php
                $utvalgVervListe = $beboer->getUtvalgVervListe();
                if (count($utvalgVervListe) == 0) {
                    echo str_replace(' ', '&nbsp;', $beboer->getRolle()->getNavn());
                } else {
                    echo '<strong>' . $utvalgVervListe[0]->getNavn() . '</strong>';
                }
                ?></td>
        </tr>
        <tr>
            <th>Antall semestre</th>
            <td><?php echo $beboer->getRomhistorikk()->getAntallSemestre(); ?></td>
        </tr>
    </table>
</div>

<?php

require_once('bunn.php');

?>
