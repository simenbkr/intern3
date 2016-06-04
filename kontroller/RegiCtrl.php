<?php

namespace intern3;

class RegiCtrl extends AbstraktCtrl {
	public function bestemHandling() {
		$aktueltArg = $this->cd->getAktueltArg();
		switch ($aktueltArg) {
			case 'rapport':
				$valgtCtrl = new RegiRapportCtrl($this->cd->skiftArg());
				break;
			case 'minregi':
				$valgtCtrl = new RegiMinregiCtrl($this->cd->skiftArg());
				break;
			case 'registatus':
				$dok = new Visning($this->cd);
				$unix = $_SERVER['REQUEST_TIME'];
				$dok->set('tabeller', array(
						'Har gjenværende regitimer'      => BrukerListe::harRegiIgjen($unix),
						'Har ikke gjenværende regitimer' => BrukerListe::harIkkeRegiIgjen($unix)
				));
				$dok->vis('regi_registatus.php');
				return;
      case 'minregi':
        $dok = new Visning($this->cd);
        $unix = $_SERVER['REQUEST_TIME'];
        $dok->vis('regi_minregi.php');
        return;
			case '':
			default:
				$dok = new Visning($this->cd);
				$dok->vis('regi.php');
				return;
		}
		$valgtCtrl->bestemHandling();
	}
}

?>
