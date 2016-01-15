<?php

namespace intern3;

class RomsjefCtrl extends AbstraktCtrl {
	public function bestemHandling() {
    $aktueltArg = $this->cd->getAktueltArg();
    if ($aktueltArg == 'beboerliste') {
    	$beboerListe = BeboerListe::aktive();
      $dok = new Visning($this->cd);
			$dok->set('beboerListe', $beboerListe);
	    $dok->vis('utvalg_romsjef_beboerliste.php');
    }
    else if ($aktueltArg == 'nybeboer') {
      $dok = new Visning($this->cd);
	    $dok->vis('utvalg_romsjef_nybeboer.php');
    }
    else if ($aktueltArg == 'lister') {
      $dok = new Visning($this->cd);
	    $dok->vis('utvalg_romsjef_lister.php');
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
      $dok->vis('utvalg_romsjef.php');
    }
  }
}

?>
