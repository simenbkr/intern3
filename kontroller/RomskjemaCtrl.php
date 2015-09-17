<?php

namespace intern3;

class RomskjemaCtrl extends AbstraktCtrl {
	public function bestemHandling() {
    //$aktueltArg = $this->cd->getAktueltArg();
    //$beboer = Beboer::medId($aktueltArg);
		$dok = new Visning($this->cd);
		$dok->vis('romskjema.php');
	}
}

?>
