<link rel="stylesheet" type="text/css" href="css/print.css"/>
<div class="container">
    <table class="table table-bordered table-responsive">
        <tr>
            <th class="tittel">Krysseliste</th>
            <th class="dato">
                Fra: <?php echo date('Y-m-d H:i', strtotime($sistFakturert)); ?>
                Til: <?php echo date('Y-m-d H:i'); ?>
            </th>
        </tr>
    </table>

    <table id="krysseliste">

        <tr>
            <th class="navn">Navn</th>
            <th class="sum">Øl</th>
            <th class="sum">Cider</th>
            <th class="sum">Carlsberg</th>
            <th class="sum">Rikdom</th>
            <th class="sum">Pant</th>
        </tr>
        <?php
        foreach($krysseListeMonthListe as $beboerID => $krysseliste){
            $beboeren = $beboerListe[$beboerID];
            ?>
            <tr>
                <td class="navn"><a href="/<?php echo $beboeren->getId();?>"><?php echo $beboeren->getFulltNavn(); ?></a></td>
                <td class="øl"><?php echo $krysseliste['Øl']; ?></td>
                <td class="cider"><?php echo $krysseliste['Cider'] ?></td>
                <td class="carlsberg"><?php echo $krysseliste['Carlsberg']; ?></td>
                <td class="rikdom"><?php echo $krysseliste['Rikdom']; ?></td>
                <td class="pant"><?php echo $krysseliste['Pant']; ?></td>
            </tr>
            <?php
        }
        ?>
    </table>


</div>