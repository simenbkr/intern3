<?php

/* @var \intern3\Arbeid $arbeid */

$godkjentNavn = "";
if ($arbeid->getGodkjent() == 0) {
    $godkjentNavn = "Ikke godkjent";
} else {
    $beboer = intern3\Bruker::medId($arbeid->getGodkjentBrukerId())->getPerson();
    if ($beboer != null) {
        $godkjentNavn = $beboer->getFulltNavn();
    }
}
?>

<td><?php echo $arbeid->getBruker()->getPerson()->getFulltNavn(); ?></td>
<td><?php echo $arbeid->getTidUtfort(); ?></td>
<td><?php
    $poly = $arbeid->getPolymorfKategori();
    if ($poly instanceof \intern3\Oppgave) {
        echo "Oppgave: ";
    } elseif ($poly instanceof \intern3\Feil) {
        echo "Feilretting: ";
    } elseif ($poly instanceof \intern3\Rapport) {
        echo "Rapport: ";
    } elseif ($poly instanceof \intern3\Arbeidskategori) {
        echo "Arbeid: ";
    }
    echo $arbeid->getPolymorfKategori()->getNavn();

    ?></td>
<td><?php echo $arbeid->getTidBrukt(); ?></td>
<td>
    <?php if (count($arbeid->getArbeidBilder()) > 0) { ?>
        <button class="btn btn-info" onclick="vis(<?php echo $arbeid->getId(); ?>)">Vis</button>
        <?php
    }
    ?>
</td>
<td><?php echo htmlspecialchars($arbeid->getKommentar()); ?></td>
<?php /*<td><?php echo $arbeid->getGodkjent() > 0 ? '<span title="Godkjent ' . substr($arbeid->getTidGodkjent(), 0, 10) . ' av ' . intern3\Bruker::medId($arbeid->getGodkjentBrukerId())->getPerson()->getFulltNavn() . '">Godkjent</span>' : 'Ubehandla'; ?>
				<br/><br/><?php echo $arbeid->getGodkjent() == 0 ? "" : "Godkjent: " . $arbeid->getTidGodkjent() . ' av ' . $godkjentNavn; ?></td>*/ ?>
<td>
    <?php
    if ($arbeid->getGodkjent()) {
        if ($arbeid->getGodkjentBruker() != null && ($personen = $arbeid->getGodkjentBruker()->getPerson()) != null) {
            echo 'Godkjent: ' . $arbeid->getTidGodkjent() . ' av ' . $personen->getFulltNavn();
        } else {
            echo 'Godkjent';
        }
    } elseif ($arbeid->getStatus() == 'Underkjent') {
        if ($arbeid->getGodkjentBruker() != null && ($personen = $arbeid->getGodkjentBruker()->getPerson()) != null) {
            echo 'Underkjent: ' . $arbeid->getTidGodkjent() . ' av ' . $personen->getFulltNavn();
        } else {
            echo 'Underkjent';
        }
    } else {
        echo $arbeid->getStatus();
    }
    ?>
</td>
<td>
    <button onclick="godkjennArbeid(<?php echo $arbeid->getId() . ($arbeid->getGodkjent() > 0 ? ', \'underkjenn\'' : ''); ?>);">
        &#x<?php echo $arbeid->getGodkjent() > 0 ? '2718' : '2714'; ?>;
    </button>
</td>
<td>
    <p><a class="btn btn-info" href="?a=utvalg/regisjef/arbeid/endre/<?php echo $arbeid->getId(); ?>">Detaljer/Endre</a>
    </p>
    <p><a class="btn btn-primary" href="?a=utvalg/regisjef/arbeid/tilbakemelding/<?php echo $arbeid->getId(); ?>">Tilbakemelding</a>
    </p>
    <p>
        <button class="btn btn-danger" onclick="underkjenn(<?php echo $arbeid->getId(); ?>)">Underkjenn</button>
    </p>
</td>
