<?php

namespace intern3;

class RomskjemaCtrl extends AbstraktCtrl {
	public function bestemHandling() {
		if (!$this->cd->getAktivBruker()->getPerson()->erBeboer()) {
			exit('kun aktive beboere');
		}
		$rom = $this->cd->getAktivBruker()->getPerson()->getRom();
		if (isset($_POST['feil']) && is_array($_POST['feil'])) {
			$this->registerAlleFeil($rom);
			Header('Location: ' . $_SERVER['REQUEST_URI']);
			exit();
		}
		$this->visEllerRegistrer($rom);
	}

	public function tvungenRegistrering() {
		$this->visEllerRegistrer($this->cd->getAktivBruker()->getPerson()->getRom(), true);
	}

	public function adminRegistrering() {
		$romId = $this->cd->getAktueltArg();
		do {
			if (!$romId || $romId == 0) {
				break;
			}
			$rom = Rom::medId($romId);
			if ($rom == null) {
				break;
			}
			$this->visEllerRegistrer($rom);
		} while(false);
		$dok = new Visning($this->cd);
		$dok->set('base', rtrim($_SERVER['REQUEST_URI'], '/') . '/0123456789');
		$dok->set('romListe', RomListe::alle());
		$dok->vis('romskjema_romliste.php');
	}

	private function visEllerRegistrer($rom, $tvungen = false) {
		if (isset($_POST['feil']) && is_array($_POST['feil'])) {
			$this->registerAlleFeil($rom);
			Header('Location: ' . $_SERVER['REQUEST_URI']);
			exit();
		}
		$dok = new Visning($this->cd);
		$dok->set('rom', $rom);
		$dok->set('tvungen', $tvungen);
		$dok->vis('romskjema.php');
	}

	private function registerAlleFeil($rom) {
		$bruker = $this->cd->getAktivBruker();
		$db = DB::getDB();
		$db->beginTransaction();
		$st = $db->prepare('INSERT INTO kvittering (bruker_id,rom_id) VALUES(:bruker_id,:rom_id);');
		$brukerId = $bruker->getId();
		$st->bindParam(':bruker_id', $brukerId);
		$romId = $rom->getId();
		$st->bindParam(':rom_id', $romId);
		$st->execute();
		$kvitteringId = $db->lastInsertId();
		$feilArray = $_POST['feil'];
		foreach($feilArray as $feilId => $kommentar) {
			if(!$kommentar || $kommentar == '') {
				continue;
			}
			$this->opprettRapport(Feil::medId($feilId), $kommentar, $kvitteringId);
		}
		$db->commit();
	}

	private function opprettRapport($feil, $kommentar , $kvitteringId) {
		if ($feil == null) {
			return;
		}
		$st = DB::getDB()->prepare('INSERT INTO rapport (kvittering_id,feil_id,prioritet_id,merknad) VALUES (:kvittering_id,:feil_id,:prioritet_id,:merknad)');
		$st->bindParam(':kvittering_id', $kvitteringId);
		$feilId = $feil->getId();
		$st->bindParam(':feil_id', $feilId);
		$prioritetId = $feil->getPrioritet()->getId();
		$st->bindParam(':prioritet_id', $prioritetId);
		$st->bindParam(':merknad', $kommentar);
		$st->execute();

		//Sende epost til Romsjef angående dette:
		//$mottaker = "regisjef@singsaker.no, romsjef@singsaker.no";
		$registrant = LogginnCtrl::getAktivBruker()->getPerson();
		$navn = $registrant->getFulltNavn();
		$mottaker = "data@singsaker.no";
		$tittel = "[SING-INTERN] Ny feil registrert av " . $registrant->getFornavn() . " på rom " . $registrant->getRom()->getNavn();
		$tekst = "<html>Dette er en automatisert melding.<br/><br/>" . $registrant->getFulltNavn() . " på rom " . $registrant->getRom()->getNavn() .
			" har opprettet en ny feil på <a href=\"https://intern.singsaker.no\" med følgende kommentar:<br/><br/>" . $kommentar .
			"<br/><br/>Feil? Ta kontakt med <a href=\"mailto:data@singsaker.no\">Datagutta</a></html>";
		Epost::sendEpost($mottaker,$tittel,$tekst);
	}
}

?>
