<?php

require_once(__DIR__ . '/../../topp_utvalg.php');

/* @var \intern3\Soknad $soknad */

?>
<div class="container">
    <div class="col-lg-12">

        <h1>Utvalget » Romsjef » Søknader » Søknad fra <?php echo $soknad->getNavn(); ?></h1>

        <hr>

        <table class="table table-responsive">

            <tr>
                <th>Telefon:</th>
                <td><?php echo $soknad->getTelefon(); ?></td>
            </tr>

            <tr>
                <th>E-post:</th>
                <td><?php echo $soknad->getEpost(); ?></td>
            </tr>

            <tr>
                <th>Født:</th>
                <td><?php echo $soknad->getFodselsar(); ?></td>
            </tr>

            <tr>
                <th>Studie:</th>
                <td><?php echo $soknad->getStudie(); ?></td>
            </tr>

            <tr>
                <th>Skole:</th>
                <td><?php echo $soknad->getSkole(); ?></td>
            </tr>

            <tr>
                <th>Fagbrev:</th>
                <td><?php echo $soknad->getFagbrev(); ?></td>
            </tr>

            <tr>
                <th>Annen kompetanse:</th>
                <td><?php echo $soknad->getKompetanse(); ?></td>
            </tr>

            <tr>
                <th>Kjenner til Sing:</th>
                <td><?php echo $soknad->getKjennskap(); ?></td>
            </tr>

            <tr>
                <th>Kjenner:</th>
                <td><?php echo $soknad->getKjenner(); ?></td>
            </tr>

            <tr>
                <th>Tekst</th>
                <td><?php echo $soknad->getTekst(); ?></td>
            </tr>
            <tr>
                <th>Bilde:</th>
                <td><img style="max-width: 300px;" src="<?php echo $soknad->getBilde(); ?>"</td>
            </tr>

            <tr>
                <th></th>
                <td><a href="?a=utvalg/romsjef/soknad/nybeboer/<?php echo $soknad->getId(); ?>"><button class="btn btn-primary">Legg til som beboer</button></a></td>
            </tr>

        </table>
    </div>
</div>
<?php


require_once(__DIR__ . '/../../../static/bunn.php');

