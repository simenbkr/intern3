<?php

namespace intern3;

class VervCtrl extends AbstraktCtrl {
	public function bestemHandling() {
		//$aktueltArg = $this->cd->getAktueltArg();
		//$verv = Verv::medId($aktueltArg);
		$vervListe = VervListe::alle();
		$dok = new Visning($this->cd);
		$dok->set('vervListe', $vervListe);
		$dok->vis('verv.php');
	}
}

?>
