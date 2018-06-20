<br/>
<hr>
<table class="table table-bordered table-responsive">
    <tr>
        <th>Dato</th>
        <th>Vaktnr.</th>
        <th>Vakthavende</th>

        <?php foreach ($drikke as $drikken) {
            if ($drikken->getId() == 1 || $drikken->getNavn() == 'Pant') {
                continue;
            }
            if ($drikken->harBlittDrukketSiden($periode_start) || $drikken->getAktiv()) { ?>
                <th><?php echo $drikken->getNavn(); ?></th>
                <th></th>
                <th></th>
                <th></th>
                <?php
            }
        }
        ?>
    </tr>


    <?php

    foreach ($journal as $vakt) {
        $datoen = substr($vakt['dato'], 0, 10); ?>
        <tr>
            <td><?php echo $datoen; ?></td>
            <td><?php echo $vakt['vaktnr']; ?></td>
            <td><?php echo $vakt['vakthavende'] == null ? '' : $vakt['vakthavende']->getFulltNavn(); ?></td>

            <?php
            foreach ($vakt['obj'] as $drikke_info) {
                $drikken = \intern3\Drikke::medId($drikke_info['drikkeId']);
                if ($drikken != null && $drikken->getId() != 1 && $drikken->getNavn() != 'Pant') {
                    if ($drikken->harBlittDrukketSiden($periode_start) || $drikken->getAktiv()) {
                        ?>
                        <td>Mottatt<br/><?php echo $drikke_info['mottatt']; ?></td>
                        <td>Påfyll<br/><?php
                            echo $drikke_info['pafyll'] > 0 ? "<b>$drikke_info[pafyll]</b>" : $drikke_info['pafyll']; ?>
                        </td>
                        <td>Avlevert<br/><?php echo $drikke_info['avlevert']; ?></td>
                        <td>Svinn<br/>
                            <?php
                            echo $drikke_info['svinn'] > 0 ? "<b>$drikke_info[svinn]</b>" : $drikke_info['svinn']; ?>
                        </td>
                        <?php
                    }
                }
            }
            ?>

        </tr>


        <?php

    }

    ?>
</table>
<br/>
<hr>