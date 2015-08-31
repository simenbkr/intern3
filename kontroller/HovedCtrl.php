<?php

namespace intern3;

class HovedCtrl extends AbstraktCtrl {
	public function bestemHandling() {
		$denneSide = $this->cd->getDenneSide();
		switch ($denneSide) {
			case 'vakt':
				$valgtCtrl = new VaktCtrl($this->cd->addSide($denneSide));
				break;
			case 'diverse':
			default:
				$valgtCtrl = new DiverseCtrl($this->cd->addSide($denneSide));
				break;
		}
		$valgtCtrl->bestemHandling();
	}
}

?>