<?php

require_once(__DIR__ . '/../static/topp.php');

/* @var $lista \intern3\Storhybelliste */

?>
<div class="col-lg-12">
    <h1>Storhybelliste</h1>

    <hr>

    Du er nummer <?php echo $persnummer; ?> på lista, og vi er nå på nummer <?php echo $lista->getVelgerNr(); ?>.

    <div class="col-lg-3">

        <h3>Ledige rom</h3>

        <table class="table table-responsive">

            <thead>
            <tr>
                <th>Romnummer</th>
                <th>Romtype</th>
            </tr>

            </thead>

            <tbody>
            <?php foreach ($lista->getLedigeRom() as $rom) {
                /* @var $rom \intern3\Rom */
                ?>

                <tr id="<?php echo $rom->getId(); ?>">
                    <td><?php echo $rom->getNavn(); ?></td>
                    <td><?php echo $rom->getType()->getNavn(); ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>

    <?php if($min_tur) { ?>
        <div class="col-lg-6">

            <p><b>Det er din tur!</b></p>
            <p>Du har 24t på å velge rom. Kontakt nestemann når du er ferdig. Nestemann er <?php $lista->getNeste()->getFulltNavn(); ?></p>

        </div>
    <?php } ?>

    <div class="col-lg-3">
        <h3>Kart</h3>
        (Klikk for å se full størrelse.)
        <a href="beboerkart/kart.jpg"><img class="img-thumbnail" src="beboerkart/kart.jpg"></a>
    </div>

    <div class="col-lg-6">
        <table class="table table-responsive table-hover grid table-condensed">
            <thead>
            <tr>
                <th class="index">Nr.</th>
                <th>Navn</th>
                <th>Ansiennitet</th>
                <th>Klassetrinn</th>
                <th>Rom forrige semester</th>
                <th>Rom neste semester</th>
            </tr>
            </thead>

            <tbody>

            <?php foreach ($lista->getRekkefolge() as $nummer => $beboer) {
                /* @var $beboer \intern3\Beboer */
                $klassen = '';
                if ($nummer === $lista->getVelgerNr()) {
                    $klassen = 'danger';
                }

                if ($nummer === $persnummer) {
                    $klassen = 'success';
                }

                if($nummer === $lista->getVelgerNr() && $nummer === $persnummer){
                    $klassen = 'warning';
                }

                ?>
                <tr id="<?php echo $beboer->getId(); ?>" class="<?php echo $klassen; ?>">
                    <td class="index"><?php echo $nummer; ?></td>
                    <td><?php echo $beboer->getFulltNavn(); ?></td>
                    <td><?php echo $beboer->getAnsiennitet(); ?></td>
                    <td><?php echo $beboer->getKlassetrinn(); ?></td>
                    <td><?php echo $lista->getFordeling()[$beboer->getId()]->getGammeltRom()->getNavn(); ?></td>
                    <td><?php echo $lista->getFordeling()[$beboer->getId()]->getNyttRomId() !== null ? $lista->getFordeling()[$beboer->getId()]->getNyttRom()->getNavn() : ''; ?></td>
                </tr>

                <?php
            } ?>
            </tbody>
        </table>
    </div>

</div>
<?php

require_once(__DIR__ . '/../static/bunn.php');

?>
