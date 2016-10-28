<?php
require_once ('topp.php');
?>
<div class="container">

    <h1>Utvalget » Vaktsjef » Krysserapport</h1>
    <h3>[ Krysserapport ] [ <a href="<?php echo $cd->getBase();?>utvalg/vaktsjef/krysserapportutskrift">Utskrift</a> ]</h3>
    <table class="table table-bordered table-responsive">
        <tr>
            <th class="tittel">Krysseliste</th>
            <th class="dato">
                Fra:
                Til: <?php echo date('Y-m-d'); ?>
            </th>
        </tr>
    </table>

    <table class="table table-bordered table-responsive">

        <tr>
            <th class="">Navn</th>
            <th class="">Øl</th>
            <th class="">Cider</th>
            <th class="">Carlsberg</th>
            <th class="">Rikdom</th>
            <th class="">Pant</th>
</tr>
            <?php
            foreach($krysseListeMonthListe as $beboerID => $krysseliste){
                $beboeren = $beboerListe[$beboerID];
                ?>
                <tr>
                <td class="navn"><a href="?a=utvalg/admin/<?php echo $beboeren->getId(); ?>/kryss"><?php echo $beboeren->getFulltNavn(); ?></a></td>
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

<?php
require_once ('bunn.php');
?>
