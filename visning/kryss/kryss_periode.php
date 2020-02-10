<table class="table table-bordered table-striped">
    <tr>
        <th>Drikke</th>
        <th>Pris</th>
        <th>Antall</th>
        <th>Prisanslag (stemmer ikke nødvendigvis)</th>
    </tr>

    <?php
    $totalt = 0;
    $antallet = 0;
    foreach ($mndkryss as $navn => $antall) {

        if($antall < 1) {
            continue;
        }

        $antallet += $antall;
        ?>
        <tr>
            <td><?php echo $navn; ?></td>
            <td><?php echo round($drikke[$navn], 2); ?>kr</td>
            <td><?php echo $antall; ?></td>
            <td><?php echo ($pris = $drikke[$navn] * $antall);
                $totalt += $pris;
                ?>kr
            </td>
        </tr>
    <?php } ?>
    <?php
    foreach ($vin_array as $kryss) {

        if($kryss['antall'] == 0) {
            continue;
        }

        $totalt += $kryss['kostnad'];
        $antallet += $kryss['antall'];
        ?>
        <tr>
            <td><?php echo $kryss['aktuell_vin']->getNavn(); ?></td>
            <td><?php echo $kryss['kostnad']; ?></td>
            <td><?php echo $kryss['antall']; ?></td>
            <td><?php echo round($kryss['kostnad'], 2); ?>kr</td>
        </tr>
        <?php
    }
    ?>
    <tr>
        <td><b>TOTALT</b></td>
        <td></td>
        <td><b><?php echo $antallet; ?></b></td>
        <td><b><?php echo $totalt; ?>kr</b></td>
    </tr>
</table>