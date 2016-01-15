<?php

namespace intern3;

class VaktsjefCtrl extends AbstraktCtrl {
	public function bestemHandling() {
    $aktueltArg = $this->cd->getAktueltArg();
    if ($aktueltArg == 'vaktsjef') {
      $dok = new Visning($this->cd);
	    $dok->vis('utvalg_vaktsjef.php');
    }
    else {
      $dok = new Visning($this->cd);
      $dok->vis('utvalg_vaktsjef.php');
    }
  }
}

?>
