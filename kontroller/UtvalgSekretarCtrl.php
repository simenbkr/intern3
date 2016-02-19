<?php

namespace intern3;

class UtvalgSekretarCtrl extends AbstraktCtrl {
	public function bestemHandling() {
    $aktueltArg = $this->cd->getAktueltArg();
    if ($aktueltArg == 'apmandsverv') {
    	$beboerListe = BeboerListe::aktive();
      $vervListe = VervListe::alle();
      $utvalg = Verv::erUtvalg();
      $dok = new Visning($this->cd);
			$dok->set('beboerListe', $beboerListe);
      $dok->set('vervListe', $vervListe);
      $dok->set('utvalg', $utvalg);
	    $dok->vis('utvalg_sekretar_apmandsverv.php');
    }
    else if ($aktueltArg == 'utvalgsverv') {
    	$beboerListe = BeboerListe::aktive();
      $vervListe = VervListe::alle();
      $dok = new Visning($this->cd);
			$dok->set('beboerListe', $beboerListe);
      $dok->set('vervListe', $vervListe);
	    $dok->vis('utvalg_sekretar_utvalgsverv.php');
    }
    else if ($aktueltArg == 'lister') {
      $dok = new Visning($this->cd);
	    $dok->vis('utvalg_sekretar_lister.php');
    }
    else if (is_numeric($aktueltArg)) {
      $beboer = Beboer::medId($aktueltArg);
      // Trenger feilhåndtering her.
      $dok = new Visning($this->cd);
      $dok->set('beboer', $beboer);
      $dok->vis('beboer_detaljer.php');
    }
    else {
      $dok = new Visning($this->cd);
      $dok->vis('utvalg_sekretar.php');
    }
  }
}

?>
