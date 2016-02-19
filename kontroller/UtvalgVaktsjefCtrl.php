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
      $beboerListe = BeboerListe::aktive();
      $dok = new Visning($this->cd);
      $dok->set('beboerListe', $beboerListe);
	    $dok->vis('utvalg_vaktsjef_vaktoversikt.php');
    }
    else {
      $dok = new Visning($this->cd);
      $dok->vis('utvalg_vaktsjef.php');
    }
  }
}

?>
