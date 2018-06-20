<link rel="stylesheet" type="text/css" href="css/gammelt_stilark.css"/>
<h2>VINKJELLER - <?php echo date('Y-m-d'); ?></h2>
<table width="100%">
    <tr>
        <th>Navn</th>
        <th>Antall</th>
        <th>Sum (eks.mva)</th>
    </tr>
    <?php
    $antall = 0;
    $sum = 0;
    foreach ($beboer_antall_vin as $beboer_antall_vin_objekt) {
        if ($beboer_antall_vin_objekt == null || $beboer_antall_vin_objekt['antall'] <= 0) {
            continue;
        }
        ?>
        <tr>
            <td><?php echo $beboer_antall_vin_objekt['beboer']->getFulltNavn(); ?></td>
            <td><?php echo $beboer_antall_vin_objekt['antall']; ?></td>
            <td><?php echo round($beboer_antall_vin_objekt['kostnad']/1.25,2); ?></td>
        </tr>
        <?php
        $antall += $beboer_antall_vin_objekt['antall'];
        $sum += round($beboer_antall_vin_objekt['kostnad']/1.25,2);

    } ?>
    <tr>
        <td><b>TOTALT</b></td>
        <td><?php echo $antall; ?></td>
        <td><?php echo $sum; ?></td>
    </tr>
</table>