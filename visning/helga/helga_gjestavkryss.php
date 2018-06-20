<div class="subcontainer" id="lista">
    <?php
    foreach ($gjesteliste_dag_gruppert as $beboers_id => $beboers_gjester) { ?>
        <table class="table table-bordered table-responsive">
            <tr class="bg-info">
                <td>
                    <b><?php echo ($beboerliste[$beboers_id] != null) ? $beboerliste[$beboers_id]->getFulltNavn() : ''; ?></b>
                </td>
            </tr>
            <?php
            foreach ($beboers_gjester as $gjest) {
                /* @var \intern3\HelgaGjest $gjest */
                $klassen = $gjest->getInne() == 0 ? 'bg-warning' : 'bg-success';
                $checked = $gjest->getInne() == 0 ? 'none' : 'checked=\"checked\"';
                $verdi = $gjest->getInne() == 0 ? 1 : 0;
                echo "<tr class=\"$klassen\" id='" . $gjest->getId() .
                    "' onclick='registrer(" . $gjest->getId() . ",$verdi)'>
                        <td>" . $gjest->getNavn() . "</td>";

                echo "<td><input id=" . $gjest->getId() . "-knapp type=\"checkbox\"  value=\"" . $gjest->getId() . "\" onclick='registrer(" . $gjest->getId() . ",$verdi)' $checked></td></tr>";
            }
            ?></table>
        <?php
    }
    ?>
</div>