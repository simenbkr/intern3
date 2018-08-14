<?php

require_once(__DIR__ . '/../static/topp.php');

?>

<div class="col-md-12">
    <h1>Regi &raquo; Min regi</h1>
</div>

<script>
    function slett(id) {
        $.ajax({
            type: 'POST',
            url: '?a=regi/minregi',
            data: 'slett=1' + '&arbeid=' + id,
            method: 'POST',
            success: function (html) {
                $(".container").replaceWith($('.container', $(html)));
            },
            error: function (req, stat, err) {
                alert(err);
            }
        });
    }

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

<div class="col-md-6 col-sm-12">
    <?php
    /*

    if (count($feil) > 0) {
        echo '	<ul style="color: #900;">' . PHP_EOL;
        foreach ($feil as $pkt) {
            echo '		<li>' . $pkt . '</li>' . PHP_EOL;
        }
        echo '	</ul>' . PHP_EOL;
    }
    */
    include(__DIR__ . '/../static/tilbakemelding.php');

    ?>
    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" enctype='multipart/form-data'>
        <table class="table table-bordered">
            <tr>
                <th>Tilhørighet</th>
                <td><select name="polymorfkategori_velger" onchange="byttPolymorfkategori(this.value);" class="form-control">
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
                    <select name="polymorfkategori_id[ymse]" id="polymorfkategori_ymse" class="form-control">
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
                    <select name="polymorfkategori_id[feil]" id="polymorfkategori_feil" class="form-control">
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
                    <select name="polymorfkategori_id[rapp]" id="polymorfkategori_rapp" class="form-control">
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
                <td><input name="tid_utfort" class="datepicker form-control"
                           value="<?php echo isset($_POST['tid_utfort']) ? $_POST['tid_utfort'] : date('Y-m-d'); ?>">
                </td>
            </tr>
            <tr>
                <th>Tid brukt</th>
                <td><input name="tid_brukt" class="form-control"
                           placeholder="0:00"<?php echo isset($_POST['tid_brukt']) ? ' value="' . $_POST['tid_brukt'] . '"' : ''; ?>>
                </td>
            </tr>
            <tr>
                <th>Kommentar</th>
                <td><textarea name="kommentar" cols="50" class="form-control"
                              rows="5"><?php echo isset($_POST['kommentar']) ? $_POST['kommentar'] : ''; ?></textarea>
                </td>
            </tr>
            <tr>
                <th>Bilder</th>
                <td>
                    <input type="file" name="file[]" multiple id="file">
                </td>
            </tr>
            <tr>
                <td></td>
                <td><input type="submit" class="btn btn-primary" name="registrer" value="Registrer"></td>
            </tr>
        </table>
    </form>
</div>

<div class="col-md-6 col-sm-12">
    <table class="table table-bordered">
        <tr>
            <th>Godkjente regitimer</th>
            <td><?php echo intern3\Funk::timerTilTidForm($regitimer[1]); ?></td>
        </tr>
        <tr>
            <th>Til behandling</th>
            <td><?php echo intern3\Funk::timerTilTidForm($regitimer[0]); ?></td>
        </tr>
        <tr>
            <th>Underkjente regitimer</th>
            <td><?php echo intern3\Funk::timerTilTidForm($regitimer[-1]); ?></td>
        </tr>
        <tr>
            <th>Antall regitimer du skal gjøre:</th>
            <td><?php echo \intern3\LogginnCtrl::getAktivBruker()->getPerson()->getRolle()->getRegitimer();?></td>
        </tr>
    </table>
</div>
<div class="container">
    <div class="col-md-12">
        <h3>Viser arbeid utført for følgende semester: <?php echo isset($_SESSION['regisemester'])
                ? intern3\Funk::semStrToReadable($_SESSION['regisemester'])
                : intern3\Funk::semStrToReadable(intern3\Funk::generateSemesterString(date('Y-m-d'))); ?></h3>
        <div class="dropdown">
            <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">Semester
                <span class="caret"></span></button>
            <ul class="dropdown-menu">
                <?php foreach($mapping as $key => $val){ ?>
                    <li><a href="#" onclick="setSemester('<?php echo $key;?>')"><?php echo $val;?></a></li>
                <?php } ?>
            </ul>

        </div>


        <table class="table table-striped table-hover">
            <thead>
            <tr>
                <th>Dato utført / registrert</th>
                <th>Kategori</th>
                <th>Tid brukt</th>
                <th>Kommentar</th>
                <th>Tilbakemelding</th>
                <th>Status</th>
                <th>Detaljer</th>
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
                    <td>
                        <?php if(strlen($arbeid->getTilbakemelding()) > 0){
                            echo $arbeid->getTilbakemelding();
                    }?>
                    </td>
                    <?php /*<td><?php echo $arbeid->getGodkjent() ? '<span title="Godkjent ' . substr($arbeid->getTidGodkjent(), 0, 10) . ' av ' . intern3\Bruker::medId($arbeid->getGodkjentBrukerId())->getPerson()->getFulltNavn() . '">Godkjent</span>' : 'Ubehandla'; ?></td>*/
                    ?><td><?php echo $arbeid->getStatus();?></td>
                    <td><a href="?a=regi/minregi/<?php echo $arbeid->getId();?>">Detaljer</a></td>
                    <td><?php echo $arbeid->getStatus() == 'Godkjent' || $arbeid->getStatus() == 'Underkjent' ? ' ' : '<button class="btn btn-danger" onclick="slett(' . $arbeid->getId() . ')">Slett</button>'; ?></td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
    </div>
</div>

<script>

    function setSemester(string){
            $.ajax({
                type: 'POST',
                url: '?a=regi/minregi',
                data: 'semester=' + string,
                method: 'POST',
                success: function (data) {
                    location.reload();
                },
                error: function (req, stat, err) {
                    alert(err);
                }
            });
    }
</script>


<?php

require_once(__DIR__ . '/../static/bunn.php');

?>
