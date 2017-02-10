<?php
require_once('topp.php');
?>
<script>
    $(document).ready(function(){
        $('.read-more-content').addClass('hide')
        // Set up a link to expand the hidden content:
            .before(' <a class="read-more-show" href="#">Mer</a>')
            // Set up a link to hide the expanded content.
            .append(' <a class="read-more-hide" href="#">Mindre</a>');
        // Set up the toggle effect:
        $('.read-more-show').on('click', function(e) {
            $(this).next('.read-more-content').removeClass('hide');
            $(this).addClass('hide');
            e.preventDefault();
        });

        $('.read-more-hide').on('click', function(e) {
            $(this).parent('.read-more-content').addClass('hide').parent().children('.read-more-show').removeClass('hide');
            e.preventDefault();
        });
    })
</script>
    <div class="container">
        <h1>Verv » Om åpmandsverv</h1>
        <div class="col-lg-12">
        <table class="table table-bordered">
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
            </tr><?php if($har_vervet){ ?>
            <tr>
                <td>Meldinger</td>

                <form action="" method="POST">
                        <td><textarea name="melding" placeholder="Ny melding"></textarea><br/><input type="submit" value="Legg til" class="btn btn-primary"></td>
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
                        <td><p><?php echo substr($melding->getTekst(),0,50); ?>
                                <?php if(strlen($melding->getTekst()) > 50) { ?><span class="read-more-content"><?php echo substr($melding->getTekst(),50,strlen($melding->getTekst())); ?></span><?php } ?></p>
                        </td>
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