<?php
require_once('topp.php');
?>
    <script>
        function mer(elem) {
            document.getElementById(elem + 'mer').style.display = 'block';
            document.getElementById(elem + 'mindre').style.display = 'none';
            console.log(document.getElementById(elem + 'mer').style.display = 'block');
        }

        function mindre(elem) {
            document.getElementById(elem + 'mer').style.display = 'none';
            document.getElementById(elem + 'mindre').style.display = 'block';
        }
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
                    <?php if ($kan_redigere_beskrivelse) { ?>
                        <form action="" method="POST">
                            <td><textarea class="form-control" rows="10" cols="50"
                                          name="beskrivelse"><?php echo $vervet->getBeskrivelse(); ?></textarea> <input
                                        type="submit" value="Endre" class="btn btn-primary"></td>
                        </form>
                        <?php
                    } else { ?>
                        <td><?php echo $vervet->getBeskrivelse(); ?></td>
                    <?php } ?>
                </tr><?php if ($har_vervet){ ?>
                <tr>
                    <td>Meldinger</td>

                    <form action="" method="POST">
                        <td><textarea class="form-control" name="melding"
                                      placeholder="Ny melding"></textarea><br/><input type="submit"
                                                                                      value="Legg til"
                                                                                      class="btn btn-primary">
                        </td>
                    </form>
                    <?php
                    } ?>
                </tr>
                <?php if (count($verv_meldinger) > 0) {
                foreach ($verv_meldinger
                
                as $verv_melding) {
                /* @var \intern3\VervMelding $verv_melding */
                
                if ($verv_melding == null || $verv_melding->getVerv() == null ||
                    $verv_melding->getBeboer() == null || strlen($verv_melding->getTekst()) < 3) {
                    continue;
                }
                ?>
                <tr>
                    <td>
                        <?php echo $verv_melding->getVerv()->getNavn(); ?>,
                        <?php echo $verv_melding->getBeboer()->getFulltNavn(); ?>
                        (<?php echo $verv_melding->getDato(); ?>):
                    </td>
                    <td><p><span id="<?php echo $verv_melding->getId(); ?>mindre">
                            
                            <?php echo substr($verv_melding->getTekst(), 0, 50); ?>

                                <a href="#" onclick="mer('<?php echo $verv_melding->getId(); ?>')">Mer</a>
                        </span>
                            
                            
                            <?php if (strlen($verv_melding->getTekst()) > 50) { ?>


                                <span id="<?php echo $verv_melding->getId(); ?>mer" style="display:none">
                            
                            <?php echo $verv_melding->getTekst(); ?>

                                    <a href="#" onclick="mindre('<?php echo $verv_melding->getId(); ?>')">Mindre</a>
                            </span>
                                <?php
                            }
                            }
                            }
                            
                            ?>
            </table>
        </div>
    </div>
<?php
require_once('bunn.php');
?>