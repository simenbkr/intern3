<link rel="stylesheet" type="text/css" href="css/print.css"/>
    <div class="col-md-12">
        <table id="beboerlistetop">
            <tr>
                <th class="left">Beboerliste</th>
                <th class="center">Singsaker Studenterhjem</th>
                <th class="right">Utskriftsdato: <?php echo date('Y-m-d');?></th>
            </tr>
        </table>
        <table class="table table-bordered table-responsive" id="beboerliste">
            <tr>
                <th class="heading">Navn</th>
                <th class="heading">Rom</th>
                <th class="heading">Telefon</th>
                <th class="heading">Epost</th>
                <th class="heading">Studie</th>
                <th class="heading">Født</th>
                <th class="heading">VIP</th>
            </tr>
            <?php

            foreach ($beboerListe as $beboer){
                ?>
                <tr>
                    <td class="data"><?php echo $beboer->getFulltNavn(); ?></td>
                    <td class="data"><?php echo $beboer->getRom()->getNavn(); ?></td>
                    <td class="data"><?php echo $beboer->getTelefon(); ?></td>
                    <td class="data"><?php echo $beboer->getEpost(); ?></td>
                    <td class="data"><?php
                        $studie = $beboer->getStudie();
                        $skole = $beboer->getSkole();
                        if ($studie == null || $skole == null) {
                            echo ' ';
                        }
                        else {
                            echo $beboer->getKlassetrinn();
                            ?>. <?php echo $studie->getNavn(); ?>&nbsp;(<?php echo $skole->getNavn(); ?>)<?php
                        }
                        ?></td>
                    <td class="data"><?php echo $beboer->getFodselsdato(); ?></td>
                    <td class="data"><?php
                        $utvalgVervListe = $beboer->getUtvalgVervListe();
                        if (count($utvalgVervListe) == 0) {
                            //echo str_replace(' ', '&nbsp;', $beboer->getRolle()->getNavn());
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