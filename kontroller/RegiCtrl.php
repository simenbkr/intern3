<?php

namespace intern3;

class RegiCtrl extends AbstraktCtrl {
	public function bestemHandling() {
		$aktueltArg = $this->cd->getAktueltArg();
		switch ($aktueltArg) {
			case 'rapport':
				$valgtCtrl = new RegiRapportCtrl($this->cd->skiftArg());
				break;
			case '':
			default:
				$dok = new Visning($this->cd);
				$dok->vis('regi.php');
				break;
		}
		$valgtCtrl->bestemHandling();
	}
}

?>
