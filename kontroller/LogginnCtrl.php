<?php

namespace intern3;

class LogginnCtrl extends AbstraktCtrl {
	public function bestemHandling() {
		$dok = new Visning($this->cd);
		$dok->vis('logginn.php');
	}
}

?>
