<?php

namespace intern3;

class UtvalgVaktsjefCtrl extends AbstraktCtrl {
	public function bestemHandling() {
		$aktueltArg = $this->cd->getAktueltArg();
		switch ($aktueltArg) {
      // case 'vaktstyring':
      //   $valgtCtrl = new UtvalgVaktsjefVaktstyringCtrl($this->cd->skiftArg());
      //   $valgtCtrl->bestemHandling();
      //   break;
      case 'generer':
        $valgtCtrl = new UtvalgVaktsjefGenererCtrl($this->cd->skiftArg());
        $valgtCtrl->bestemHandling();
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
        $torild = Ansatt::medId(1);
        $dok = new Visning($this->cd);
        $dok->set('torild', $torild);
        $dok->set('denneUka', @date('W'));
        $dok->set('detteAret', @date('Y'));
        $dok->set('beboerListe', $beboerListe);
        $dok->vis('utvalg_vaktsjef_vaktstyring.php');
        break;
      case 'vaktstyring_settvakt':
        $beboer = Beboer::medId($this->cd->getArg($this->cd->getAktuellArgPos()+1));
        if ($beboer == NULL) {
          exit();
        }
        else {
          $dok = new Visning($this->cd);
          $dok->set('beboer', $beboer);
          $dok->vis('utvalg_vaktsjef_vaktstyring_settvakt.php');
        }
        break;
      case 'vaktstyring_modal':
        $beboerListe = BeboerListe::harVakt();
        $dok = new Visning($this->cd);
        $dok->set('beboerListe', $beboerListe);
        $dok->vis('utvalg_vaktsjef_vaktstyring_modal.php');
        break;
			default:
				$dok = new Visning($this->cd);
				$dok->vis('utvalg_vaktsjef.php');
				break;
		}
	}
}

?>
