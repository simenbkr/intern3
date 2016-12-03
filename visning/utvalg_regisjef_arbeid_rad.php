				<td><?php echo $arbeid->getBruker()->getPerson()->getFulltNavn(); ?></td>
				<td><?php echo $arbeid->getTidUtfort(); ?></td>
				<td><?php

	if (!isset($oppg)) {
		if (get_class($arbeid->getPolymorfKategori()) == 'Oppgave') {
			echo '<a href="' . $cd->getBase() . '/oppgave/' . $arbeid->getPolymorfKategori()->getId() . '">' . $arbeid->getPolymorfKategori()->getNavn() . '</a>';
		}
		else {
			echo $arbeid->getPolymorfKategori()->getNavn();
		}
	}

?></td>
				<td><?php echo $arbeid->getTidBrukt(); ?></td>
				<td><?php echo htmlspecialchars($arbeid->getKommentar()); ?></td>
				<td><?php echo $arbeid->getGodkjent() > 0 ? '<span title="Godkjent ' . substr($arbeid->getTidGodkjent(), 0, 10) . ' av ' . intern3\Bruker::medId($arbeid->getGodkjentBrukerId())->getPerson()->getFulltNavn() . '">Godkjent</span>' : 'Ubehandla'; ?></td>
				<td><button onclick="godkjennArbeid(<?php echo $arbeid->getId() . ($arbeid->getGodkjent() > 0 ? ', \'underkjenn\'' : ''); ?>);">&#x<?php echo $arbeid->getGodkjent() > 0 ? '2718' : '2714'; ?>;</button></td>
				<td><a class="btn btn-info" href="?a=utvalg/regisjef/arbeid/endre/<?php echo $arbeid->getId(); ?>">Endre</a></td>
