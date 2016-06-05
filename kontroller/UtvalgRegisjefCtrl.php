<?php

namespace intern3;

class UtvalgRegisjefCtrl extends AbstraktCtrl {
	public function bestemHandling() {
		$aktueltArg = $this->cd->getAktueltArg();
		switch ($aktueltArg) {
			case 'arbeid':
				$valgtCtrl = new UtvalgRegisjefArbeidCtrl($this->cd->skiftArg());
				break;
      case 'oppgave':
        $valgtCtrl = new UtvalgRegisjefOppgaveCtrl($this->cd->skiftArg());
        break;
			default:
				$dok = new Visning($this->cd);
				$dok->vis('utvalg_regisjef.php');
				return;
		}
		$valgtCtrl->bestemHandling();
	}
}

?>
