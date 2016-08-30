<?php

namespace intern3;

class UtvalgRegisjefOppgaveCtrl extends AbstraktCtrl {
	public function bestemHandling() {
    $oppgaveListe = OppgaveListe::alle();
		$dok = new Visning($this->cd);
    $dok->set('oppgaveListe', $oppgaveListe);
		$dok->vis('utvalg_regisjef_oppgave.php');
	}
}

?>
