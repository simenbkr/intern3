<?php
require_once('topp_utvalg.php');
?>
<link rel="stylesheet" type="text/css" href="css/dataTables.css"/>
<script type="text/javascript" src="js/dataTables.js"></script>
<script>
    $(document).ready(function(){
        $('#tabellen').DataTable();
    });
</script>

<div class="container">

    <h1>Utvalget » Vaktsjef » Detalj-kryss</h1>
    <h2>Detaljkryss for <?php echo $beboer->getFulltNavn(); ?></h2>

    <table id="tabellen" class="table table-bordered table-responsive" data-toggle="table">
        <thead>
        <tr>
            <th data-sortable="true">Navn</th>
            <th data-sortable="true">Antall</th>
            <th data-sortable="true">Tid</th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($beboersKrysseliste as $drikkeKryss) {
            $kryssListe = $drikkeKryss->getKryssListe();
            foreach ($kryssListe as $kryss) { ?>
                <tr>
                    <td><?php echo $drikkeKryss->getDrikke()->getNavn(); ?></td>
                    <td><?php echo $kryss->antall; ?></td>
                    <td data-month="<?php echo strtotime($kryss->tid); ?>"><?php echo $kryss->tid; ?></td>
                </tr>
                <?php
            }
        }
        ?>
        </tbody>
    </table>
</div>


<?php
require_once('bunn.php');
?>
