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
      $antallVakter = Vakt::antallVakter();
      $antallUfordelte = Vakt::antallUfordelte();
      $antallUbekreftet = Vakt::antallUbekreftet();
      $dok = new Visning($this->cd);
      $dok->set('beboerListe', $beboerListe);
      $dok->set('antallVakter', $antallVakter);
      $dok->set('antallUfordelte', $antallUfordelte);
      $dok->set('antallUbekreftet', $antallUbekreftet);
	    $dok->vis('utvalg_vaktsjef_vaktoversikt.php');
    }
    else if ($aktueltArg == 'vaktstyring') {
      $dok = new Visning($this->cd);
      $dok->set('denneUka', @date('W'));
      $dok->set('detteAret', @date('Y'));
	    $dok->vis('utvalg_vaktsjef_vaktstyring.php');
    }
    else if ($aktueltArg == 'generer') {
      $dok = new Visning($this->cd);
      $dok->vis('utvalg_vaktsjef_generer.php');
    }
    else {
      $dok = new Visning($this->cd);
      $dok->vis('utvalg_vaktsjef.php');
    }
  }
}

?>
