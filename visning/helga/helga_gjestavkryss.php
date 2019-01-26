<div class="subcontainer" id="lista">
    <?php
    foreach ($gjesteliste_dag_gruppert as $beboers_id => $beboers_gjester) { ?>
        <table class="table table-bordered table-responsive">
            <tr class="bg-info">
                <td>
                    <b><?php echo ($beboerliste[$beboers_id] != null) ? $beboerliste[$beboers_id]->getFulltNavn() : ''; ?></b>
                </td>
                <td></td>
            </tr>
            <?php
            foreach ($beboers_gjester as $gjest) {
                /* @var \intern3\HelgaGjest $gjest */
                $klassen = $gjest->getInne() == 0 ? 'bg-warning' : 'bg-success';
                $checked = $gjest->getInne() == 0 ? 'none' : 'checked=\"checked\"';
                $class = $gjest->getInne() == 0 ? 'btn-danger' : 'btn-info';
                $verdi = $gjest->getInne() == 0 ? 1 : 0;
                $tekst = $gjest->getInne() == 0 ? 'Registrer' : 'Fjern';
                ?>

                <tr id="<?php echo $gjest->getId(); ?>" class="<?php echo $klassen; ?>">
                    <td id="<?php echo $gjest->getId(); ?>-navn"
                        onclick="registrer(<?php echo $gjest->getId(); ?>, <?php echo $verdi; ?>)">
                        <?php echo $gjest->getNavn(); ?>
                    </td>

                    <td>
                        <button id="<?php echo $gjest->getId(); ?>-knapp" class="btn <?php echo $class; ?>"
                                onclick="registrer(<?php echo $gjest->getId(); ?>, <?php echo $verdi; ?>)">
                            <?php echo $tekst; ?>
                        </button>
                    </td>
                </tr>
                <?php
            }
            ?></table>
        <?php
    }
    ?>
</div>