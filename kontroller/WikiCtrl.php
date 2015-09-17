<?php

namespace intern3;

class WikiCtrl extends AbstraktCtrl {
	public function bestemHandling() {
		$dok = new Visning($this->cd);
		$dok->vis('wiki.php');
	}
}

?>
