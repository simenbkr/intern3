<?php

namespace intern3;

class HovedCtrl extends AbstraktCtrl {
	public function bestemHandling() {
		// if (LogginnCtrl::getAktivBruker()==NULL) {
		// 	$valgtCtrl = new LogginnCtrl($this->cd->skiftArg());
		// 	$valgtCtrl->bestemHandling();
		// 	return;
		// }
		$aktueltArg = $this->cd->getAktueltArg();
		switch ($aktueltArg) {
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
			case 'diverse':
			default:
				$valgtCtrl = new DiverseCtrl($this->cd->skiftArg());
				break;
		}
		$valgtCtrl->bestemHandling();
	}
}

?>
