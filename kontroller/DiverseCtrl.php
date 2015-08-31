<?php

namespace intern3;

class DiverseCtrl extends AbstraktCtrl {
	public function bestemHandling() {
		$dok = new Visning('diverse.php');
		$dok->vis();
	}
}

?>