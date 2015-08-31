<?php

namespace intern3;

class HovedCtrl extends AbstraktCtrl {
	public function bestemHandling() {
		$sisteArg = $this->cd->getSisteArg();
		switch ($sisteArg) {
			case 'vakt':
				$valgtCtrl = new VaktCtrl($this->cd->addArg($sisteArg));
				break;
			case 'diverse':
			default:
				$valgtCtrl = new DiverseCtrl($this->cd->addArg($sisteArg));
				break;
		}
		$valgtCtrl->bestemHandling();
	}
}

?>