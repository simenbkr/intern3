<?php
require_once(__DIR__ . '/../topp_utvalg.php');

if (!isset($beboer) || $beboer == null) {
    exit();
}
?>
    <script>
        $(function () {
            $("#datepicker").datepicker({dateFormat: "yy-mm-dd"});
        });
    </script>
    <div class="container">
        <h1>Utvalget » Romsjef » Gammel Beboerliste » Endre (gammel beboer)
            <b><?php echo $beboer->getFulltNavn(); ?></b></h1>
    
        <?php include(__DIR__ . '/../../static/tilbakemelding.php'); ?>

        <form action="" method="post">
            <input type="hidden" name="beboerid"
                   value="<?php echo !isset($beboer) || $beboer == null ? '' : $beboer->getId(); ?>">
            <table class="table table-bordered table-responsive">
                <tr>
                    <td>Fornavn:</td>
                    <td><input type="text" name="fornavn" class="form-control"
                               value="<?php echo !isset($beboer) || $beboer == null ? '' : $beboer->getFornavn(); ?>">
                    </td>
                </tr>
                <tr>
                    <td>Mellomnavn:</td>
                    <td><input type="text" name="mellomnavn" class="form-control"
                               value="<?php echo !isset($beboer) || $beboer == null ? '' : $beboer->getMellomnavn(); ?>">
                    </td>
                </tr>
                <tr>
                    <td>Etternavn:</td>
                    <td><input type="text" name="etternavn" class="form-control"
                               value="<?php echo !isset($beboer) || $beboer == null ? '' : $beboer->getEtternavn(); ?>">
                    </td>
                </tr>
                <tr>
                    <td>Født (år-mnd-dag):</td>
                    <td><input type="text" id="datepicker" name="fodselsdato" class="form-control"
                               value="<?php echo !isset($beboer) || $beboer == null ? '' : $beboer->getFodselsdato(); ?>">
                    </td>
                </tr>
                <tr>
                    <td>Adresse:</td>
                    <td><input type="text" name="adresse" class="form-control"
                               value="<?php echo !isset($beboer) || $beboer == null ? '' : $beboer->getAdresse(); ?>">
                    </td>
                </tr>
                <tr>
                    <td>Postnummer:</td>
                    <td><input type="text" name="postnummer" class="form-control"
                               value="<?php echo !isset($beboer) || $beboer == null ? '' : $beboer->getPostnummer(); ?>">
                    </td>
                </tr>
                <tr>
                    <td>Telefon:</td>
                    <td><input type="text" name="mobil" class="form-control"
                               value="<?php echo !isset($beboer) || $beboer == null ? '' : $beboer->getTelefon(); ?>">
                    </td>
                </tr>

                <tr>
                    <td>E-post:</td>
                    <td><input type="text" name="epost" class="form-control"
                               value="<?php echo !isset($beboer) || $beboer == null ? '' : $beboer->getEpost(); ?>">
                    </td>
                </tr>

                <tr>
                    <td>Skole:</td>
                    <td>
                        <select name="skole_id" class="form-control">
                            <?php
                            foreach ($skoleListe as $skole) {
                                ?>
                                <option <?php if (isset($beboer) && $beboer != null && $skole->getId() == $beboer->getSkoleId()) {
                                    echo 'selected="selected"';
                                } ?> value="<?php echo $skole->getId(); ?>"><?php echo $skole->getNavn(); ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Studie:</td>
                    <td>
                        <select name="studie_id" class="form-control">
                            <?php
                            foreach ($studieListe as $studie) {
                                ?>
                                <option <?php if (isset($beboer) && $beboer != null && $studie->getId() == $beboer->getStudieId()) {
                                    echo 'selected="selected"';
                                } ?> value="<?php echo $studie->getId(); ?>"><?php echo $studie->getNavn(); ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Klasse:</td>
                    <td>
                        <select name="klasse" class="form-control">
                            <option <?php if (isset($beboer) && $beboer != null && $beboer->getKlassetrinn() == '1') {
                                echo 'selected="selected"';
                            } ?> value="1">1
                            </option>
                            <option <?php if (isset($beboer) && $beboer != null && $beboer->getKlassetrinn() == '2') {
                                echo 'selected="selected"';
                            } ?> value="2">2
                            </option>
                            <option <?php if (isset($beboer) && $beboer != null && $beboer->getKlassetrinn() == '3') {
                                echo 'selected="selected"';
                            } ?> value="3">3
                            </option>
                            <option <?php if (isset($beboer) && $beboer != null && $beboer->getKlassetrinn() == '4') {
                                echo 'selected="selected"';
                            } ?> value="4">4
                            </option>
                            <option <?php if (isset($beboer) && $beboer != null && $beboer->getKlassetrinn() == '5') {
                                echo 'selected="selected"';
                            } ?> value="5">5
                            </option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Betalt alkodepositum:</td>
                    <td><input type="checkbox"
                               name="alkodepositum"<?php if (isset($beboer) && $beboer != null && $beboer->harAlkoholdepositum()) {
                            echo ' checked="checked"';
                        } ?>></td>
                </tr>
                <tr>
                    <td>Rolle:</td>
                    <td>
                        <select name="rolle_id" class="form-control">
                            <?php
                            foreach ($rolleListe as $rolle) {
                                ?>
                                <option <?php if (isset($beboer) && $beboer != null && $rolle->getId() == $beboer->getRolleId()) {
                                    echo 'selected="selected"';
                                } ?> value="<?php echo $rolle->getId(); ?>"><?php echo $rolle->getNavn(); ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <?php if (isset($beboer) && $beboer != null) { ?> <!-- fjerner romhistorikk på _nybeboer.php -->
                    <tr>
                        <td>Romhistorikk:</td>
                        <td><?php

                            if (isset($beboer) && $beboer != null) {
                                foreach ($beboer->getRomhistorikk()->getPerioder() as $periode) {
                                    echo "Rom: " . intern3\Rom::medId($periode->romId)->getNavn() . ' Innflyttet: ' . $periode->innflyttet . ', Utflyttet: ' . $periode->utflyttet . '<br>';
                                }
                            }

                            ?></td>
                    </tr>
                <?php } ?> <!-- slutt -->
                <tr>
                    <td>Rom:</td>
                    <td>
                        <select name="rom_id" class="form-control">
                            <?php
                            foreach ($romListe as $rom) {
                                ?>
                                <option <?php if (isset($beboer) && $beboer != null && $rom->getId() == $beboer->getRomId()) {
                                    echo 'selected="selected"';
                                } ?> value="<?php echo $rom->getId(); ?>"><?php echo $rom->getNavn(); ?></option>
                                <?php
                            }
                            ?>
                        </select>
                        <button class="btn btn-danger" onclick="flyttinn()">Flytt inn</button>

                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td><input class="btn btn-primary" type="submit"
                               value="<?php echo !isset($beboer) || $beboer == null ? 'Legg til' : 'Endre'; ?>"></td>
                </tr>
            </table>
        </form>
    </div>

    <script>
        function flyttinn() {
            $.ajax({
                type: 'POST',
                url: '?a=utvalg/romsjef/flyttinn',
                data: 'id=' + '<?php echo $beboer->getId(); ?>',
                method: 'POST',
                success: function (data) {
                    //location.reload();
                },
                error: function (req, stat, err) {
                    alert(err);
                }
            });

        }
    </script>

<?php
require_once(__DIR__ . '/../../static/bunn.php');

?>