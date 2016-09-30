<?php

namespace intern3;

class UtvalgVaktsjefVaktstyringCtrl extends AbstraktCtrl {
	public function bestemHandling() {
		$aktueltArg = $this->cd->getAktueltArg();
		switch ($aktueltArg) {
			case 'settvakt':
				$beboer = Beboer::medId($this->cd->getArg($this->cd->getAktuellArgPos()+1));
        if ($beboer == NULL) {

        }
        else {
  				$dok = new Visning($this->cd);
          $dok->set('beboer', $beboer);
  				$dok->vis('utvalg_vaktsjef_vaktstyring_settvakt.php');
        }
				break;
			case 'modal':
        $beboerListe = BeboerListe::harVakt();
        $dok = new Visning($this->cd);
        $dok->set('beboerListe', $beboerListe);
        $dok->vis('utvalg_vaktsjef_vaktstyring_modal.php');
        break;
      case 'vaktstyring':
			default:
        $beboerListe = BeboerListe::harVakt();
        $torild = Ansatt::medId(1);
        $dok = new Visning($this->cd);
        $dok->set('torild', $torild);
        $dok->set('denneUka', @date('W'));
        $dok->set('detteAret', @date('Y'));
        $dok->set('beboerListe', $beboerListe);
        $dok->vis('utvalg_vaktsjef_vaktstyring.php');
        break;
		}
	}
}

?>
