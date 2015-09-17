<?php

namespace intern3;

class HelgaCtrl extends AbstraktCtrl {
	public function bestemHandling() {
		//$aktueltArg = $this->cd->getAktueltArg();
		//$helga = Helga::medId($aktueltArg);
		$dok = new Visning($this->cd);
		$dok->vis('helga.php');
	}
}

?>
