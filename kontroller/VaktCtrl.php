<?php

namespace intern3;

class VaktCtrl extends AbstraktCtrl {
	public function bestemHandling() {
		$aktueltArg = $this->cd->getAktueltArg();
		$vaktbytteListe = VaktbytteListe::etterVakttype();
		if ($aktueltArg == 'bytte') {
			$dok = new Visning($this->cd);
			$dok->set('vaktbytteListe', $vaktbytteListe);
			$dok->vis('vakt_bytte_liste.php');
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
