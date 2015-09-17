<?php

namespace intern3;

class RombytteCtrl extends AbstraktCtrl {
	public function bestemHandling() {
		//$aktueltArg = $this->cd->getAktueltArg();
		//$rombytte = rom::medId($aktueltArg);
		$dok = new Visning($this->cd);
		$dok->vis('rombytte.php');
	}
}

?>
