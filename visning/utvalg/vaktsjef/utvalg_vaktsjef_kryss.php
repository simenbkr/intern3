<?php
require_once(__DIR__ . '/../topp_utvalg.php');
?>

<link rel="stylesheet" type="text/css" href="css/dataTables.css"/>
<script type="text/javascript" src="js/dataTables.js"></script>
<div class="container">
    <div class="col-lg-12">
    <h1>Utvalget » Vaktsjef » Kryss minutt for minutt</h1>


        <table id="tabellen" class="table table-bordered table-responsive" data-toggle="table">
            <thead>
            <tr>
                <th data-sortable="true">Navn</th>
                <th data-sortable="true">Drikkenavn</th>
                <th data-sortable="true">Antall</th>
                <th data-sortable="true">Tid</th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach($alleKryss as $beboernavn => $beboersKrysseliste) {
                foreach ($beboersKrysseliste as $drikkeKryss) {
                    $kryssListe = $drikkeKryss->getKryssListe();
                    foreach ($kryssListe as $kryss) { ?>
                        <tr>
                            <td><?php echo $beboernavn;?></td>
                            <td><?php echo $drikkeKryss->getDrikke()->getNavn(); ?></td>
                            <td><?php echo $kryss->antall; ?></td>
                            <td data-month="<?php echo strtotime($kryss->tid); ?>"><?php echo $kryss->tid; ?></td>
                        </tr>
                        <?php
                    }
                }
            }
            ?>
            </tbody>
        </table>


    </div>
</div>

<script>
    $(document).ready(function(){
        $('#tabellen').DataTable();
    });
</script>

<?php
require_once(__DIR__ . '/../../static/bunn.php');
?>
