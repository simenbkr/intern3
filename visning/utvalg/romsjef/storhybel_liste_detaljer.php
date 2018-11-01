<?php

require_once(__DIR__ . '/../topp_utvalg.php');

?>
    <div class="container">
        <div class="col-lg-12">
            <h1>Utvalget &raquo; Romsjef &raquo; StorhybellisteLISTE &raquo; <?php echo $lista->getNavn(); ?></h1>

            <table class="table table-responsive table-bordered">

                <tr>
                    <td>Navn</td>
                    <td><?php echo $lista->getNavn(); ?></td>
                </tr>

                <tr>
                    <td>Status</td>
                    <td><?php echo $lista->erAktiv() ? 'Aktiv' : 'Inaktiv'; ?></td>
                </tr>





            </table>


        </div>
    </div>

<?php

require_once(__DIR__ . '/../../static/bunn.php');

