<?php

namespace intern3;

class RegiCtrl extends AbstraktCtrl {
	public function bestemHandling() {
		//$aktueltArg = $this->cd->getAktueltArg();
		//$regi = Regi::medId($aktueltArg);
		$dok = new Visning($this->cd);
		$dok->vis('regi.php');
	}
}

?>
