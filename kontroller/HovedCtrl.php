<?php

namespace intern3;

class HovedCtrl extends AbstraktCtrl {
	public function bestemHandling() {
		$aktueltArg = $this->cd->getAktueltArg();
		switch ($aktueltArg) {
			case 'beboer':
				$valgtCtrl = new BeboerCtrl($this->cd->skiftArg());
				break;
			case 'vakt':
				$valgtCtrl = new VaktCtrl($this->cd->skiftArg());
				break;
			case 'utleie':
					$valgtCtrl = new UtleieCtrl($this->cd->skiftArg());
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
