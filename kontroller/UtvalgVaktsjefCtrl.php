<?php

namespace intern3;

class UtvalgVaktsjefCtrl extends AbstraktCtrl {
	public function bestemHandling() {
		$aktueltArg = $this->cd->getAktueltArg();
		switch ($aktueltArg) {
			case 'vaktsjef':
				$dok = new Visning($this->cd);
				$dok->vis('utvalg_vaktsjef.php');
				break;
			case 'vaktoversikt':
				$beboerListe = BeboerListe::harVakt();
				$antallVakter = Vakt::antallVakter();
				$antallUfordelte = Vakt::antallUfordelte();
				$antallUbekreftet = Vakt::antallUbekreftet();
				$dok = new Visning($this->cd);
				$dok->set('beboerListe', $beboerListe);
				$dok->set('antallVakter', $antallVakter);
				$dok->set('antallUfordelte', $antallUfordelte);
				$dok->set('antallUbekreftet', $antallUbekreftet);
				$dok->vis('utvalg_vaktsjef_vaktoversikt.php');
				break;
			case 'vaktstyring':
        $beboerListe = BeboerListe::harVakt();
				$dok = new Visning($this->cd);
				$dok->set('denneUka', @date('W'));
				$dok->set('detteAret', @date('Y'));
        $dok->set('beboerListe', $beboerListe);
				$dok->vis('utvalg_vaktsjef_vaktstyring.php');
				break;
			case 'generer':
				$valgtCtrl = new UtvalgVaktsjefGenererCtrl($this->cd->skiftArg());
				$valgtCtrl->bestemHandling();
				break;
			default:
				$dok = new Visning($this->cd);
				$dok->vis('utvalg_vaktsjef.php');
				break;
		}
	}
}

?>
