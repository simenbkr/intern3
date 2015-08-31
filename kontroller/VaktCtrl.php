<?php

namespace intern3;

class VaktCtrl extends AbstraktCtrl {
	public function bestemHandling() {
		$dok = new Visning('vakt_vaktliste.php');
		$dok->set('denneUka', @date('W'));
		$dok->set('detteAret', @date('Y'));
		$dok->vis();
	}
}

?>