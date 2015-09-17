<?php

namespace intern3;

class UtflyttingCtrl extends AbstraktCtrl {
	public function bestemHandling() {
		$dok = new Visning($this->cd);
		$dok->vis('utflytting.php');
	}
}

?>
