<?php

namespace intern3;

class HovedCtrl extends AbstraktCtrl {
	public function bestemHandling() {
		$aktivBruker = LogginnCtrl::getAktivBruker();
		if ($aktivBruker == null) {
			$valgtCtrl = new LogginnCtrl($this->cd->skiftArg());
			$valgtCtrl->bestemHandling();
			return;
		}
		$this->cd->setAktivBruker($aktivBruker);
		$aktueltArg = $this->cd->getAktueltArg();
		if ($aktueltArg <> 'logginn' && $aktivBruker->getPerson()->erBeboer()) {
			$kvittering = Kvittering::detteSemesterMedRomId($aktivBruker->getPerson()->getRomId());
			if ($kvittering == null || $kvittering->getId() == null) {
				$valgtCtrl = new RomskjemaCtrl($this->cd->skiftArg());
				$valgtCtrl->tvungenRegistrering();
				return;
			}
		}
		$this->bestemKontroller();
	}
	public function bestemKontroller() {
		switch ($this->cd->getAktueltArg()) {
			case 'beboer':
				$valgtCtrl = new BeboerCtrl($this->cd->skiftArg());
				break;
			case 'vakt':
				$valgtCtrl = new VaktCtrl($this->cd->skiftArg());
				break;
			case 'regi':
				$valgtCtrl = new RegiCtrl($this->cd->skiftArg());
				break;
			case 'verv':
				$valgtCtrl = new VervCtrl($this->cd->skiftArg());
				break;
			case 'kryss':
				$valgtCtrl = new KryssCtrl($this->cd->skiftArg());
				break;
			case 'wiki':
				$valgtCtrl = new WikiCtrl($this->cd->skiftArg());
				break;
			case 'utleie':
				$valgtCtrl = new UtleieCtrl($this->cd->skiftArg());
				break;
			case 'helga':
				$valgtCtrl = new HelgaCtrl($this->cd->skiftArg());
				break;
			case 'rombytte':
				$valgtCtrl = new RombytteCtrl($this->cd->skiftArg());
				break;
			case 'profil':
				$valgtCtrl = new ProfilCtrl($this->cd->skiftArg());
				break;
			case 'romskjema':
			  $valgtCtrl = new RomskjemaCtrl($this->cd->skiftArg());
				break;
			case 'utflytting':
				$valgtCtrl = new UtflyttingCtrl($this->cd->skiftArg());
				break;
			case 'logginn':
				$valgtCtrl = new LogginnCtrl($this->cd->skiftArg());
				break;
			case 'utvalg':
				$valgtCtrl = new UtvalgCtrl($this->cd->skiftArg());
				break;
			case 'diverse':
			default:
				$valgtCtrl = new DiverseCtrl($this->cd->skiftArg());
				break;
		}
		$valgtCtrl->bestemHandling();
	}
}

?>
