<link rel="stylesheet" type="text/css" href="css/print.css"/>
<div class="col-md-12">
    <table id="beboerlistetop">
        <tr>
            <th class="left">Vaktliste</th>
            <th class="center">Singsaker Studenterhjem</th>
            <th class="right">Utskriftsdato: <?php echo date('Y-m-d'); ?></th>
        </tr>
    </table>
<?php

$denneUka = @date('W');
$detteAret = @date('Y');
    $ukeStart = strtotime('last sunday - 6 days, midnight');

    foreach (range($denneUka, $denneUka > 26 ? date('W', mktime(0, 0, 0, 12, 31, date('Y'))) : 26) as $uke) {
    $ukeStart = strtotime('+1 week', $ukeStart);
    ?>
    <table class="table-bordered table" id="beboerliste">
        <tr>
            <th class="data" style="width:5.5%;"><span class="hidden-sm hidden-xs">Uke&nbsp;</span><?php echo $uke; ?></th>
            <th class="data" style="width:13.5%;">M<span class="hidden-xs">an<span
                        class="hidden-sm">dag</span></span>&nbsp;<?php echo date('d/m', $ukeStart); ?></th>
            <th class="data" style="width:13.5%;">T<span class="hidden-xs">ir<span
                        class="hidden-sm">sdag</span></span>&nbsp;<?php echo date('d/m', strtotime('+1 day', $ukeStart)); ?>
            </th>
            <th class="data" style="width:13.5%;">O<span class="hidden-xs">ns<span
                        class="hidden-sm">dag</span></span>&nbsp;<?php echo date('d/m', strtotime('+2 day', $ukeStart)); ?>
            </th>
            <th class="data" style="width:13.5%;">T<span class="hidden-xs">or<span
                        class="hidden-sm">sdag</span></span>&nbsp;<?php echo date('d/m', strtotime('+3 day', $ukeStart)); ?>
            </th>
            <th class="data" style="width:13.5%;">F<span class="hidden-xs">re<span
                        class="hidden-sm">dag</span></span>&nbsp;<?php echo date('d/m', strtotime('+4 day', $ukeStart)); ?>
            </th>
            <th class="data" style="width:13.5%;">L<span class="hidden-xs">ør<span
                        class="hidden-sm">dag</span></span>&nbsp;<?php echo date('d/m', strtotime('+5 day', $ukeStart)); ?>
            </th>
            <th class="data" style="width:13.5%;">S<span class="hidden-xs">øn<span
                        class="hidden-sm">dag</span></span>&nbsp;<?php echo date('d/m', strtotime('+6 day', $ukeStart)); ?>
            </th>
        </tr>
        <?php
        foreach (range(1, 4) as $vakttype) {
            ?>
            <tr class="data">
                <td class="data"><?php echo $vakttype; ?>.<span class="hidden-sm hidden-xs">&nbsp;vakt</span><br>&nbsp;</td>
                <?php
                foreach (range(0, 6) as $ukedag) {
                    $vakt = intern3\Vakt::medDatoVakttype(date('Y-m-d', strtotime('+' . $ukedag . ' day', $ukeStart)), $vakttype);

                    if ($vakt == null && $vakttype == 2 && $ukedag >= 0 && $ukedag <= 4) {
                        echo '			<td class="data" class="celle_graa">Torild Fivë</td>' . PHP_EOL;
                        continue;
                    }
                    if ($vakt == null) {
                        echo '			<td class="data"> </td>' . PHP_EOL;
                        continue;
                    }
                    if ($vakt->erLedig()) {
                        // echo '			<td style="text-align: center;"><input type="button" class="btn btn-info" value="Ledig"></td>' . PHP_EOL;  //Knapp for ledige vakter
                        echo '			<td class="data"> </td>' . PHP_EOL;
                        continue;
                    }
                    $bruker = $vakt->getBruker();
                    echo '			<td class="data"';
                    if ($vakt->erLedig() || $vakt->vilBytte()) {
                        echo ' class="tekst_ledigevakter"';
                    } else if ($bruker <> null && $bruker->getId() == $cd->getAktivBruker()->getId()) {
                        echo ' class="tekst_dinevakter"';
                    }
                    echo '>';
                    if ($bruker == null) {
                        echo ' ';
                    } else {
                        echo $bruker->getPerson()->getFulltNavn();
                    }
                    echo '</td>' . PHP_EOL;
                }
                ?>
            </tr>
            <?php
        }
        ?>
    </table>
    <?php
    }
    ?>
</div>