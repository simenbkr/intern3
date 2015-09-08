<?php

namespace intern3;

class BeboerCtrl extends AbstraktCtrl {
	public function bestemHandling() {
		$aktueltArg = $this->cd->getAktueltArg();
		if (is_numeric($aktueltArg)) {
			$beboer = Beboer::medId($aktueltArg);
			// Trenger feilhåndtering her.
			$dok = new Visning($this->cd);
			$dok->set('beboer', $beboer);
			$dok->vis('beboer_detaljer.php');
		}
		else {
			$beboerListe = BeboerListe::aktive();
			$dok = new Visning($this->cd);
			$dok->set('beboerListe', $beboerListe);
			$dok->vis('beboer_beboerliste.php');
		}
	}
}

?>