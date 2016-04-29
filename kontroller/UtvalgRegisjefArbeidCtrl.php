<?php

namespace intern3;

class UtvalgRegisjefArbeidCtrl extends AbstraktCtrl {
	public function bestemHandling() {
		$sideinndeling = new SideinndelData();
		$sideinndeling->setPerSide(200);
		$sideinndeling->setSide($this->cd->getAktueltArg());
		$dok = new Visning($this->cd);
		$dok->set('arbeidListe', ArbeidListe::alle($sideinndeling));
		$dok->set('sideinndeling', $sideinndeling);
		$dok->vis('utvalg_regisjef_arbeid.php');
	}
}

?>
