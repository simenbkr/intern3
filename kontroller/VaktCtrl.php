<?php

namespace intern3;

class VaktCtrl extends AbstraktCtrl {
	public function bestemHandling() {
		$aktueltArg = $this->cd->getAktueltArg();
		if ($aktueltArg == 'bytte') {
			$dok = new Visning($this->cd);
			$dok->set('vaktbytte', $vaktbytte);
			$dok->vis('vakt_bytte.php');
		}
		else {
			$dok = new Visning($this->cd);
			$dok->set('denneUka', @date('W'));
			$dok->set('detteAret', @date('Y'));
			$dok->vis('vakt_vaktliste.php');
		}
	}
}

?>
