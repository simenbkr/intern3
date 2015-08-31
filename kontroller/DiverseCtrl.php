<?php

namespace intern3;

class DiverseCtrl extends AbstraktCtrl {
	public function bestemHandling() {
		$dok = new Visning($this->cd);
		$dok->vis('diverse.php');
	}
}

?>