<?php

namespace intern3;

class UtvalgKosesjefCtrl extends AbstraktCtrl {
	public function bestemHandling() {
    $aktueltArg = $this->cd->getAktueltArg();
    if ($aktueltArg == 'utleie') {
      $dok = new Visning($this->cd);
	    $dok->vis('utvalg_kosesjef_utleie.php');
    }
    else {
      $dok = new Visning($this->cd);
      $dok->vis('utvalg_kosesjef.php');
    }
  }
}

?>
