<?php
require_once(__DIR__ . '/../topp_utvalg.php');

?>
    <link rel="stylesheet" type="text/css" href="css/dataTables.css"/>
    <script type="text/javascript" src="js/dataTables.js"></script>

    <script>
        $(document).ready(function () {
            var table = $('#tabellen').DataTable({
                "paging": false,
                "searching": false,
                "order": [[2, "asc"]]
            });
        });
    </script>

    <div class="container">
        <div class="col-lg-6">
            <h1>Utvalget » Vaktsjef » Drikke</h1>
            <hr>

            <?php include(__DIR__ . '/../../static/tilbakemelding.php'); ?>

            <div class="col-md-3">
                <form action="" method="POST">
                    <table id="tabellen" class="table table-bordered table-responsive small">
                        <input type="hidden" name="thingy" value="1">
                        <thead>
                        <tr>
                            <th>Kategori</th>
                            <th>Pris</th>
                            <th>Aktiv</th>
                            <th>Farge</th>
                            <th>Drikke</th>
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
                                <td><?php echo $drikken->getFarge(); ?></td>
                                <td><?php echo $drikken->getDrikke(); ?></td>
                            </tr>
                            <?php
                        }
                        ?>
                        </tbody>
                    </table>
                </form>
            </div>

            <div class="col-md-12">
                <form action="" method="post" enctype="multipart/form-data">
                    <table class="table table-bordered table-responsive">
                        <tr>
                            <td>Navn:</td>
                            <td><input class="form-control" type="text" name="navn" value="" placeholder="Navn på drikke/kategori"></td>
                        </tr>
                        <tr>
                            <td>Pris:</td>
                            <td><input class="form-control" type="text" name="pris" value="" placeholder="0.0"></td>
                        </tr>
                        <tr>
                            <td>Farge (på krysselista):</td>
                            <td><input class="form-control" type="color" name="farge" value="#000"></td>
                        </tr>
                        <tr>
                            <td>Drikke:</td>
                            <td><input class="form-control" type="text" name="drikke1" value="" placeholder="'Liste med tilhørende drikke...'"></td>
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