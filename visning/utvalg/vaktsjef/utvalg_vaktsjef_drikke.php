<?php
require_once(__DIR__ . '/../topp_utvalg.php');

?>
    <link rel="stylesheet" type="text/css" href="css/dataTables.css"/>
    <script type="text/javascript" src="js/dataTables.js"></script>

    <script>
        $(document).ready(function () {
            $('#tabellen').DataTable({
                "paging": false,
                "searching": false,
                "order": [[2, "asc"]]
            });
        });
    </script>

    <div class="container">
        <div class="row">
            <h1>Utvalget » Vaktsjef » Drikke</h1>
            <hr>
            <?php include(__DIR__ . '/../../static/tilbakemelding.php'); ?>
        </div>
        <div class="row">

            <!-- Tabell med drikke: -->
            <div class="col-md-6">
                <form action="" method="POST">
                    <table id="tabellen" class="table table-bordered table-responsive small">
                        <input type="hidden" name="thingy" value="1">
                        <thead>
                            <tr>
                                <th>Kategori</th>
                                <th>Pris</th>
                                <th>Aktiv</th>
                                <th>Farge</th>
                                <th>Kommentar</th>
                                <th>Først</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($drikke as $drikken) { ?>
                                <tr>
                                    <td>
                                        <a href="?a=utvalg/vaktsjef/endre_drikke/<?php echo $drikken->getId(); ?>"><?php echo $drikken->getNavn(); ?></a>
                                    </td>
                                    <td><?php echo $drikken->getPris(); ?></td>
                                    <td><?php echo $drikken->getAktiv() ? 'Aktiv' : 'Inaktiv'; ?></td>
                                    <td><canvas height="15vh" width="60vh" style="background-color:<?php echo $drikken->getFarge(); ?> "></td>
                                    <td><?php echo $drikken->getKommentar(); ?></td>
                                    <td><?php echo $drikken->erForst() ? 'Først!' : ''; ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </form>
            </div>
            <!-- Form til å legge til drikke: -->
            <div class="col-md-6">
                <form action="" method="post" enctype="multipart/form-data">
                    <table class="table table-bordered table-responsive">
                        <tr>
                            <th>Navn:</th>
                            <td><input class="form-control" type="text" name="navn" value="" placeholder="Navn på drikke/kategori"></td>
                        </tr>
                        <tr>
                            <th>Pris:</th>
                            <td><input class="form-control" type="text" name="pris" value="" placeholder="0.0"></td>
                        </tr>
                        <tr>
                            <th>Farge (på krysselista):</th>
                            <td><input class="form-control" type="color" name="farge" value="#000"></td>
                        </tr>
                        <tr>
                            <th>Kommentar:</th>
                            <td><textarea class="form-control" name="kommentar" placeholder="'Liste med tilhørende drikke...'"></textarea></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td><input class="btn btn-primary" type="submit" value="Legg til"></td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
    </div>
<?php
require_once(__DIR__ . '/../../static/bunn.php');
?>