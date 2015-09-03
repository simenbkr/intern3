<?php

namespace intern3;

class BeboerCtrl extends AbstraktCtrl {
	public function bestemHandling() {
		$beboerListe = BeboerListe::aktive();
		$dok = new Visning($this->cd);
		$dok->set('beboerListe', $beboerListe);
		$dok->vis('beboer_beboerliste.php');
	}
}

?>