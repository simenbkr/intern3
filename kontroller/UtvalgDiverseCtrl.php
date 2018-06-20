<?php

namespace intern3;

class UtvalgDiverseCtrl extends AbstraktCtrl {
	public function bestemHandling() {
		$aktueltArg = $this->cd->getAktueltArg();
		$dok = new Visning($this->cd);
		$dok->vis('utvalg/utvalg.php');
	}
}

?>
