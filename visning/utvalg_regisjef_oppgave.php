<?php

require_once('topp_utvalg.php');

?>

<div class="col-md-12">
    <h1>Utvalget &raquo; Regisjef &raquo; Oppgave</h1>
</div>

<script>
    function fjern(id) {
        $.ajax({
            type: 'POST',
            url: '?a=utvalg/regisjef/oppgave/',
            data: 'fjern=' + id,
            method: 'POST',
            success: function (data) {
                $('#oppgave_' + id).html(data);
                location.reload();
            },
            error: function (req, stat, err) {
                alert(err);
            }
        });
    }

    function godkjenn(id) {
        $.ajax({
            type: 'POST',
            url: '?a=utvalg/regisjef/oppgave/',
            data: 'godkjenn=' + id,
            method: 'POST',
            success: function (data) {
                $('#oppgave_' + id).html(data);
                location.reload();
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
    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
        <table class="table table-bordered">
            <tr>
                <th>Navn</th>
                <td><input name="navn" <?php echo isset($_POST['navn']) ? ' value="' . $_POST['navn'] . '"' : ''; ?>>
                </td>
            </tr>
            <tr>
                <th>Prioritet</th>
                <td><input
                        name="prioritet" <?php echo isset($_POST['prioritet']) ? ' value="' . $_POST['prioritet'] . '"' : ''; ?>>
                </td>
            </tr>
            <tr>
                <th>Anslag timer</th>
                <td><input name="timer"
                           placeholder="0:00"<?php echo isset($_POST['timer']) ? ' value="' . $_POST['timer'] . '"' : ''; ?>>
                </td>
            </tr>
            <tr>
                <th>Anslag personer</th>
                <td><input
                        name="personer" <?php echo isset($_POST['personer']) ? ' value="' . $_POST['personer'] . '"' : ''; ?>>
                </td>
            </tr>
            <tr>
                <th>Beskrivelse</th>
                <td><textarea name="beskrivelse" cols="50"
                              rows="5"><?php echo isset($_POST['beskrivelse']) ? $_POST['beskrivelse'] : ''; ?></textarea>
                </td>
            </tr>
            <tr>
                <td></td>
                <td><input type="submit" class="btn btn-primary" name="registrer" value="Registrer"></td>
            </tr>
        </table>
    </form>
</div>

<div class="col-md-12 table-responsive">
    <table class="table table-striped table-hover">
        <thead>
        <tr>
            <th>Navn</th>
            <th>Prioritet</th>
            <th>Anslag timer</th>
            <th>Anslag personer</th>
            <th>Påmeldte</th>
            <th>Beskrivelse</th>
            <th>Opprettet</th>
            <th>Godkjent</th>
            <th>Godkjenn/Fjern</th>
        </tr>
        </thead>
        <tbody>
        <?php

        foreach ($oppgaveListe as $oppgave) {
            $id = $oppgave->getId();
            $navn = $oppgave->getNavn();
            $pri = $oppgave->getPrioritetId();
            $timer = $oppgave->getAnslagTimer();
            $personer = $oppgave->getAnslagPersoner();
            $beskrivelse = $oppgave->getBeskrivelse();
            $oppretta = $oppgave->getTidOppretta();
            $tidgodkjent = $oppgave->getTidGodkjent();
            $godkjent = $oppgave->getGodkjent();
            $godkjentav = '';
            //TODO FIX THIS SHIT MAN
            if ($godkjent == -1) {
                $godkjentav = intern3\Bruker::medId($oppgave->getGodkjentBrukerId()) == null ? intern3\Bruker::medId($oppgave->getGodkjentBruker())->getPerson()->getFulltNavn() : intern3\Beboer::medId($oppgave->getGodkjentBrukerId())->getFulltNavn();
                //$godkjentav = $oppgave->getGodkjentBrukerId();
            }

            ?>
            <tr id="<?php echo $id;?>">
                <td><?php echo $navn; ?> </td>
                <td><?php echo $pri; ?> </td>
                <td><?php echo $timer; ?></td>
                <td><?php echo $pri; ?></td>
                <td><?php echo $personer; ?></td>
                <td><?php echo $beskrivelse; ?></td>
                <td><?php echo $oppretta; ?></td>
                <td><?php echo $oppgave->getTidGodkjent() != null ? '<span title="Godkjent av ' . $godkjentav
                        . '" > ' . $oppgave->getTidGodkjent() . '</span>' : ''; ?></td>

                <td><?php if ($godkjent == 0) { ?>
                    <button class="btn btn-default" onclick="godkjenn(<?php echo $id; ?>)">Godkjenn</button><?php } ?>
                    <button class="btn btn-default" onclick="fjern(<?php echo $id; ?>)">Fjern</button>
                </td>
            </tr>

            <?php
        }


        ?>
        </tbody>
    </table>
</div>

<?php

require_once('bunn.php');

?>
