<?php

namespace intern3;

class KryssCtrl extends AbstraktCtrl {
	public function bestemHandling() {
		//$aktueltArg = $this->cd->getAktueltArg();
		//$kryss = Kryss::medId($aktueltArg);
		$dok = new Visning($this->cd);
		$dok->vis('kryss.php');
	}
}

?>
