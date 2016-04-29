<?php

namespace intern3;

class UtvalgRomsjefCtrl extends AbstraktCtrl {
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
		else if ($aktueltArg == 'endrebeboer') {
      $beboerListe = BeboerListe::aktive();
			$dok = new Visning($this->cd);
      $dok->set('beboerListe', $beboerListe);
			$dok->vis('utvalg_romsjef_endrebeboer.php');
		}
		else if ($aktueltArg == 'endrebeboer_tabell') {
      $beboer = Beboer::medId($this->cd->getArg($this->cd->getAktuellArgPos()+1));
      if ($beboer == null) {
        exit();
      }
      $skoleListe = SkoleListe::alle();
      $studieListe = StudieListe::alle();
      $rolleListe = RolleListe::alle();
      $romListe = RomListe::alle();
			$dok = new Visning($this->cd);
      $dok->set('beboer', $beboer);
      $dok->set('skoleListe', $skoleListe);
      $dok->set('studieListe', $studieListe);
      $dok->set('rolleListe', $rolleListe);
      $dok->set('romListe', $romListe);
			$dok->vis('utvalg_romsjef_endrebeboer_tabell.php');
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
