<?php

namespace intern3;

class UtvalgVaktsjefCtrl extends AbstraktCtrl {
	public function bestemHandling() {
    $aktueltArg = $this->cd->getAktueltArg();
    if ($aktueltArg == 'vaktsjef') {
      $dok = new Visning($this->cd);
	    $dok->vis('utvalg_vaktsjef.php');
    }
    else if ($aktueltArg == 'vaktoversikt') {
      $beboerListe = BeboerListe::harVakt();
      $antallUfordelte = Vakt::antallUfordelte();
      $dok = new Visning($this->cd);
      $dok->set('beboerListe', $beboerListe);
      $dok->set('antallUfordelte', $antallUfordelte);
	    $dok->vis('utvalg_vaktsjef_vaktoversikt.php');
    }
    else if ($aktueltArg == 'vaktstyring') {
      $dok = new Visning($this->cd);
      $dok->set('denneUka', @date('W'));
      $dok->set('detteAret', @date('Y'));
	    $dok->vis('utvalg_vaktsjef_vaktstyring.php');
    }
    else {
      $dok = new Visning($this->cd);
      $dok->vis('utvalg_vaktsjef.php');
    }
  }
}

?>
