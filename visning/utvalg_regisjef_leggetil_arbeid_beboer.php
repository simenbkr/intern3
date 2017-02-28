<?php
require_once ('topp_utvalg.php');
?>
<script>

    function byttPolymorfkategori(id) {
        $('#polymorfkategori_ymse').hide();
        $('#polymorfkategori_feil').hide();
        $('#polymorfkategori_rapp').hide();
        $('#polymorfkategori_oppg').hide();
        switch (id) {
            case 'ymse':
                $('#polymorfkategori_ymse').show();
                break;
            case 'feil':
                $('#polymorfkategori_feil').show();
                break;
            case 'rapp':
                $('#polymorfkategori_rapp').show();
                break;
            case 'oppg':
                $('#polymorfkategori_oppg').show();
                break;
        }
    }
    $(document).ready(function () {
        byttPolymorfkategori('<?php echo isset($_POST['polymorfkategori_velger']) ? $_POST['polymorfkategori_velger'] : 'ymse'; ?>');
    });
</script>
<div class="container">
    <h1>Utvalget » Regisjef » Legg til regi for <b><?php echo $beboeren->getFulltNavn();?></b></h1>
    <hr>
    <p>Legg til strafferegi ved å velge strafferegi fra kategori og legge inn en (positiv) verdi i tid brukt enten på form time:sekund eller time som desimaltall.</p>
    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
        <div class="col-md-8 table-responsive">
        <table class="table table-bordered">
            <tr>
                <th>Tilhørighet</th>
                <td><select name="polymorfkategori_velger" onchange="byttPolymorfkategori(this.value);">
                        <option
                            value="ymse"<?php echo !isset($_POST['polymorfkategori_velger']) || $_POST['polymorfkategori_velger'] == 'ymse' ? ' selected="selected"' : ''; ?>>
                            Generelt arbeid
                        </option>
                        <option
                            value="feil"<?php echo isset($_POST['polymorfkategori_velger']) && $_POST['polymorfkategori_velger'] == 'feil' ? ' selected="selected"' : ''; ?>>
                            Generell feil
                        </option>
                        <option
                            value="rapp"<?php echo isset($_POST['polymorfkategori_velger']) && $_POST['polymorfkategori_velger'] == 'rapp' ? ' selected="selected"' : ''; ?>>
                            Spesifikk feil
                        </option>
                        <option
                            value="oppg"<?php echo isset($_POST['polymorfkategori_velger']) && $_POST['polymorfkategori_velger'] == 'oppg' ? ' selected="selected"' : ''; ?>>
                            Spesifikk oppgave
                        </option>
                    </select></td>
            </tr>
            <tr>
                <th>Kategori</th>
                <td>
                    <select name="polymorfkategori_id[ymse]" id="polymorfkategori_ymse">
                        <?php

                        foreach (intern3\ArbeidskategoriListe::aktiveListe() as $ak) {
                            echo '						<option value="' . $ak->getId() . '"';
                            if (isset($_POST['polymorfkategori_id']['ymse']) && $_POST['polymorfkategori_id']['ymse'] == $ak->getId()) {
                                echo ' selected="selected"';
                            }
                            echo '>' . $ak->getNavn() . '</option>' . PHP_EOL;
                        }

                        ?>
                    </select>
                    <select name="polymorfkategori_id[feil]" id="polymorfkategori_feil">
                        <?php

                        foreach (intern3\FeilkategoriListe::alle() as $fk) {
                            echo '						<optgroup label="' . $fk->getNavn() . '">' . PHP_EOL;
                            foreach ($fk->getFeilListe() as $f) {
                                echo '							<option value="' . $f->getId() . '"';
                                if (isset($_POST['polymorfkategori_id']['feil']) && $_POST['polymorfkategori_id']['feil'] == $f->getId()) {
                                    echo ' selected="selected"';
                                }
                                echo '>' . $f->getNavn() . '</option>' . PHP_EOL;
                            }
                            echo '						</optgroup>' . PHP_EOL;
                        }

                        ?>
                    </select>
                    <select name="polymorfkategori_id[rapp]" id="polymorfkategori_rapp">
                        <optgroup label="Mine ansvarsområder">
                            <?php

                            foreach (intern3\RapportListe::medBrukerId_brukerensAnsvarsomrade($this->cd->getAktivBruker()->getId()) as $r) {
                                echo '							<option value="' . $r->getId() . '"';
                                if (isset($_POST['polymorfkategori_id']['rapp']) && $_POST['polymorfkategori_id']['rapp'] == $r->getId()) {
                                    echo ' selected="selected"';
                                }
                                echo '>' . $r->getFeil()->getNavn() . ' &laquo;' . $r->getMerknad() . '&raquo; (' . $r->getKvittering()->getRom()->getNavn() . ')</option>' . PHP_EOL;
                            }

                            ?>
                        </optgroup>
                        <optgroup label="Mine egne rapporter">
                            <?php

                            foreach (intern3\RapportListe::medBrukerId_brukerensEgne($this->cd->getAktivBruker()->getId()) as $r) {
                                echo '							<option value="' . $r->getId() . '"';
                                if (isset($_POST['polymorfkategori_id']['rapp']) && $_POST['polymorfkategori_id']['rapp'] == $r->getId()) {
                                    echo ' selected="selected"';
                                }
                                echo '>' . $r->getFeil()->getNavn() . ' &laquo;' . $r->getMerknad() . '&raquo; (' . $r->getKvittering()->getRom()->getNavn() . ')</option>' . PHP_EOL;
                            }

                            ?>
                        </optgroup>
                    </select>
                    <!--<select name="polymorfkategori_id[oppg]" id="polymorfkategori_oppg">
<?php

                    //foreach (intern3\OppgaveListe::aktiveListe() as $o) {
                    //	echo '						<option value="' . $o->getId() . '"';
                    //	if (isset($_POST['polymorfkategori_id']['oppg']) && $_POST['polymorfkategori_id']['oppg'] == $o->getId()) {
                    //		echo ' selected="selected"';
                    //	}
                    //	echo '>' . $o->getNavn() . '</option>' . PHP_EOL;
                    //}

                    ?>
					</select>-->
                </td>
            </tr>
            <tr>
                <th>Dato utført</th>
                <td><input name="tid_utfort" class="datepicker"
                           value="<?php echo isset($_POST['tid_utfort']) ? $_POST['tid_utfort'] : date('Y-m-d'); ?>">
                </td>
            </tr>
            <tr>
                <th>Tid brukt</th>
                <td><input name="tid_brukt"
                           placeholder="0:00"<?php echo isset($_POST['tid_brukt']) ? ' value="' . $_POST['tid_brukt'] . '"' : ''; ?>>
                </td>
            </tr>
            <tr>
                <th>Kommentar</th>
                <td><textarea name="kommentar" cols="50"
                              rows="5"><?php echo isset($_POST['kommentar']) ? $_POST['kommentar'] : ''; ?></textarea>
                </td>
            </tr>
            <tr>
                <td></td>
                <td><input type="submit" class="btn btn-primary" name="registrer" value="Registrer"></td>
            </tr>
        </table>
    </form>
</div>
    <div class="col-md-3">
        <table class="table table-bordered">
            <tr>
                <th>Godkjente regitimer</th>
                <td><?php echo intern3\Funk::timerTilTidForm($regitimer[1]); ?></td>
            </tr>
            <tr>
                <th>Avventer godkjenning</th>
                <td><?php echo intern3\Funk::timerTilTidForm($regitimer[0]); ?></td>
            </tr>
            <tr>
                <th>Antall regitimer beboeren skal gjøre:</th>
                <td><?php echo $beboeren->getRolle()->getRegitimer();?></td>
            </tr>
        </table>
    </div>
    <div class="col-md-12 table-responsive">
        <table class="table table-striped table-hover">
            <thead>
            <tr>
                <th>Dato utført / registrert</th>
                <th>Kategori</th>
                <th>Tid brukt</th>
                <th>Kommentar</th>
                <th>Status</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php

            foreach ($arbeidListe as $arbeid) {
                ?>
                <tr>
                    <td><?php echo substr($arbeid->getTidUtfort(), 0, 10) . ' / ' . substr($arbeid->getTidRegistrert(), 0, 10); ?></td>
                    <td><?php echo $arbeid->getPolymorfKategori()->getNavn(); ?></td>
                    <td><?php echo intern3\Funk::timerTilTidForm($arbeid->getSekunderBrukt() / 3600); ?></td>
                    <td><?php echo htmlspecialchars($arbeid->getKommentar()); ?></td>
                    <td><?php echo $arbeid->getGodkjent() ? '<span title="Godkjent ' . substr($arbeid->getTidGodkjent(), 0, 10) . ' av ' . intern3\Bruker::medId($arbeid->getGodkjentBrukerId())->getPerson()->getFulltNavn() . '">Godkjent</span>' : 'Ubehandla'; ?></td>
                    <td><?php echo $arbeid->getGodkjent() ? ' ' : '<button class="btn btn-danger" onclick="slett(' . $arbeid->getId() . ')">Slett</button>'; ?></td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
    </div>
</div>
</div>
<?php
require_once ('bunn.php');
?>