<?php

namespace intern3;

class VervCtrl extends AbstraktCtrl {
	public function bestemHandling() {
		//$aktueltArg = $this->cd->getAktueltArg();
		//$verv = Verv::medId($aktueltArg);
		$dok = new Visning($this->cd);
		$dok->vis('verv.php');
	}
}

?>
