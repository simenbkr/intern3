<?php

require_once(__DIR__ . '/../topp_utvalg.php');

?>

<div class="col-md-12">
    <h1>Utvalget &raquo; Romsjef &raquo; Beboerliste</h1>
    <p>[ Beboerliste ] [ <a href="<?php echo $cd->getBase(); ?>beboer/utskrift">Utskriftsvennlig</a> ] [ <a
                href="<?php echo $cd->getBase(); ?>beboer/statistikk">Statistikk</a> ]</p>
    <p></p>
    <?php include(__DIR__ . '/../../static/tilbakemelding.php'); ?>
    <?php if ($showTable) { ?>

        <div class="col-sm-6">
            <table class="table table-responsive">
                <tr>
                    <td>Antall beboere</td>
                    <td><?php echo count($beboerListe); ?></td>
                </tr>

                <tr>
                    <td>Full regi</td>
                    <td><?php echo $fullregi; ?></td>
                </tr>

                <tr>
                    <td>Full vakt</td>
                    <td><?php echo $fullvakt; ?></td>
                </tr>

                <tr>
                    <td>Halv vakt/halv regi</td>
                    <td><?php echo $halv; ?></td>
                </tr>

            </table>
        </div>

    <?php }
      ?>

    <a href="?a=utvalg/romsjef/eksporter"><button class="btn btn-warning pull-right">Eksporter til CSV</button></a> <br/> <br/>
    <input type="button" class="btn btn-primary pull-right" data-toggle="modal" data-target="#modal-nyttStudie" value="Legg til nytt studie"/>

    <table class="table-bordered table" id="tabellen">
        <thead>
        <tr>
            <th>Navn</th>
            <th>Rom</th>
            <th>Telefon</th>
            <th>Epost</th>
            <th>Studie</th>
            <th>Født</th>
            <th>Rolle</th>
        </tr>
        </thead>
        <tbody>
        <?php

        foreach ($beboerListe as $beboer) {

            /* @var \intern3\Beboer $beboer */

            if($beboer == null  || $beboer->getRom() == null){
                continue;
            }


            try {

                ?>
                <tr>
                    <td>
                        <a href="?a=utvalg/romsjef/beboerliste/<?php echo $beboer->getId(); ?>"><?php echo $beboer->getFulltNavn(); ?></a>
                    </td>
                    <td><?php echo $beboer->getRom()->getNavn(); ?></td>
                    <td><?php echo $beboer->getTelefon(); ?></td>
                    <td><a href="mailto:<?php echo $beboer->getEpost(); ?>"><?php echo $beboer->getEpost(); ?></a></td>
                    <td><?php
                        $studie = $beboer->getStudie();
                        $skole = $beboer->getSkole();
                        if ($studie == null || $skole == null) {
                            echo ' ';
                        } else {
                            echo $beboer->getKlassetrinn();
                            ?>.
                            <a href="?a=studie/<?php echo $studie->getId(); ?>"><?php echo $studie->getNavn(); ?></a>&nbsp;(
                            <a href="?a=skole/<?php echo $skole->getId(); ?>"><?php echo $skole->getNavn(); ?></a>)<?php
                        }
                        ?></td>
                    <td><?php echo $beboer->getFodselsdato(); ?></td>
                    <td><?php
                        $utvalgVervListe = $beboer->getUtvalgVervListe();
                        if (count($utvalgVervListe) == 0) {
                            echo str_replace(' ', '&nbsp;', $beboer->getRolle()->getNavn());
                        } else {
                            echo '<strong>' . $utvalgVervListe[0]->getNavn() . '</strong>';
                        }
                        ?></td>
                </tr>
                <?php
                } catch (Exception $e) {}
        }
        ?>
        </tbody>
    </table>

    <div class="modal fade" aria-hidden="true" id="modal-nyttStudie" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Skriv inn navnet på nytt studie</h4>
                </div>
                <div class="modal-body">
                    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
                        <input type="hidden" value="studienavn">
                        <div class="container">
                            <div class="row">
                                <div class="col-sm-3">
                                    <p><input class="form-control" name="studienavn" placeholder="Nytt studie" type="text" required/></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-3">
                                    <input type="submit" class="btn btn-md btn-primary" value="Legg til">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Lukk</button>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" type="text/css" href="css/dataTables.css"/>
<script type="text/javascript" src="js/dataTables.js"></script>

<script>

    $(document).ready(function () {
        var table = $('#tabellen').DataTable({
            "paging": false,
            "searching": false,
            //"scrollY": "500px"
        });
    });

</script>


<?php

require_once(__DIR__ . '/../../static/bunn.php');

?>
