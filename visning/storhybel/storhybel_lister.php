<?php

require_once(__DIR__ . '/../static/topp.php');


?>
<div class="container">
    <div class="col-lg-12">
        <h1>Storhybelliste » Liste</h1>

        <hr>
        <table class="table table-responsive table-hover">
            <thead>
            <tr>
                <th>Navn</th>
                <th>Nåværende velger</th>
                <th>Neste velger</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($listene as $lista) {
            /* @var \intern3\Storhybelliste $lista */
            ?>
                <tr>
                    <td><a href="?a=storhybel/<?php echo $lista->getId(); ?>"><?php echo $lista->getNavn(); ?></a></td>
                    <td><?php echo $lista->getVelger()->getNavn(); ?></td>
                    <td><?php echo $lista->getNeste()->getNavn(); ?></td>
                </tr>

            <?php } ?>
            </tbody>

        </table>
    </div>
</div>

<?php

require_once (__DIR__ . '/../static/bunn.php');

