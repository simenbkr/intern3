<?php

namespace intern3;

class UtvalgRegisjefCtrl extends AbstraktCtrl {
	public function bestemHandling() {
		$aktueltArg = $this->cd->getAktueltArg();
		if ($aktueltArg == 'regisjef') {
			$dok = new Visning($this->cd);
			$dok->vis('utvalg_regisjef.php');
		}
		else {
			$dok = new Visning($this->cd);
			$dok->vis('utvalg_regisjef.php');
		}
	}
}

?>
