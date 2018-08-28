<?php
require_once(__DIR__ . '/../topp_utvalg.php');
?>

<div class="col-lg-12">


    <h1>Utvalget » Regisjef » Regiliste</h1>
    <hr>

    <script>


    </script>


    <div class="col-md-12">

        <div class="alert alert-success fade in" id="success" style="margin: auto; margin-top: 5%; display:none">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <p id="tilbakemelding-text"></p>
        </div>
        <p></p>

        <table class="table table-bordered table-responsive" id="tabellen">
            <thead>
            <tr>
                <th>Velg</th>
                <th>Navn</th>
                <th>Rom</th>
                <th>Studie</th>
                <th>Født</th>
                <th>Rolle</th>
            </tr>
            </thead>
            <tbody>
            <?php

            foreach ($beboerliste as $beboer) {
                /* @var \intern3\Beboer $beboer */
                if ($beboer == null || !isset($beboer)) {
                    continue;
                }
                ?>
                <tr>
                    <td>
                        <button class="btn btn-primary" id="btn-<?php echo $beboer->getId(); ?>"
                                onclick="velg(<?php echo $beboer->getId(); ?>)">Velg
                        </button>
                    </td>
                    <td>
                        <a href="<?php echo $cd->getBase(); ?>beboer/<?php echo $beboer->getId(); ?>">
                            <?php echo $beboer->getFulltNavn(); ?></a>
                    </td>

                    <td><?php echo $beboer->getRom()->getNavn(); ?></td>
                    <td><?php
                        $studie = $beboer->getStudie();
                        $skole = $beboer->getSkole();
                        if ($studie == null || $skole == null) {
                            echo ' ';
                        } else {
                            echo $beboer->getKlassetrinn();
                            ?>. <a href="<?php echo $cd->getBase(); ?>studie/<?php echo $studie->getId(); ?>">
                                <?php echo $studie->getNavn(); ?></a>&nbsp;(<a href="<?php echo $cd->getBase(); ?>
                                skole/<?php echo $skole->getId(); ?>"><?php echo $skole->getNavn(); ?></a>)
                            <?php
                        }
                        ?></td>
                    <td><?php echo $beboer->getFodselsdato(); ?></td>
                    <td><?php
                        $utvalgVervListe = $beboer->getUtvalgVervListe();
                        if (count($utvalgVervListe) == 0) {
                            echo str_replace(' ', '&nbsp;', $beboer->getRolle()->getNavn());
                        } else {
                            echo '<strong>' . $utvalgVervListe[0]->getNavn() . '</strong>';
                        }
                        ?></td>
                </tr>
                <?php
            }

            ?>
            </tbody>
        </table>

    </div>




</div>
<?php
require_once(__DIR__ . '/../../static/bunn.php');
