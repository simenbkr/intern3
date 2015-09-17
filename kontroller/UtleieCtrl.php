<?php

namespace intern3;

class UtleieCtrl extends AbstraktCtrl {
	public function bestemHandling() {
		//$aktueltArg = $this->cd->getAktueltArg();
		//$utleie = Utleie::medId($aktueltArg);
		$dok = new Visning($this->cd);
		$dok->vis('utleie_liste.php');
	}
}

?>
