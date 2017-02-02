<?php
require_once('topp.php');
?>
    <div class="container">
        <h1>Verv » Om åpmandsverv</h1>
        <div class="col-lg-12">
        <table class="table table-responsive">
            <tr>
                <td>Verv:</td>
                <td><?php echo $vervet->getNavn(); ?></td>
            </tr>
            <tr>
                <td>Åpmend:</td>
                <td><?php
                    $str = "";
                    foreach ($vervet->getApmend() as $beboer) {
                        if ($beboer != null) {
                            $str .= '<a href="?a=beboer/' . $beboer->getId() . '">' . $beboer->getFulltNavn() . '</a>, ';
                        }
                    }
                    echo rtrim($str, ', '); ?></td>
            </tr>
            <tr>
                <td>Antall regitimer:</td>
                <td><?php echo $vervet->getRegitimer(); ?></td>
            </tr>
            <tr>
                <td>Beskrivelse:</td>
                <?php if($kan_redigere_beskrivelse){ ?>
                    <form action="" method="POST">
                    <td><input type="text" name="beskrivelse" value="<?php echo $vervet->getBeskrivelse();?>">    <input type="submit" value="Endre" class="btn btn-primary"></td>
                    </form>
                    <?php
                } else { ?>
                <td><?php echo $vervet->getBeskrivelse(); ?></td>
    <?php } ?>
            </tr>
            <tr>
                <td>Meldinger</td>
                <?php if($har_vervet){ ?>
                <form action="" method="POST">
                        <td><input type="text" name="melding" placeholder="Ny melding">     <input type="submit" value="Endre" class="btn btn-primary"></td>
            </form>
            <?php
                } else { ?>
                <td></td>
    <?php } ?>
            </tr>
            <?php if (count($verv_meldinger) > 0) {
                foreach ($verv_meldinger as $melding) { ?>
                    <tr>
                        <td><?php echo ($melding->getBeboer()) != null ? $melding->getBeboer()->getFulltNavn() : '';?> den <?php echo date('Y-m-d', strtotime($melding->getDato()));?>:</td>
                        <td class="text-center"><?php echo $melding->getTekst();?></td>
                        </tr>
                    <?php
                }
            }
            ?>
        </table>
            </div>
    </div>
<?php
require_once('bunn.php');
?>