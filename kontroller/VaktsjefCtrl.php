<?php

namespace intern3;

class UtvalgVaktsjefCtrl extends AbstraktCtrl {
	public function bestemHandling() {
    $aktueltArg = $this->cd->getAktueltArg();
    if ($aktueltArg == 'vaktsjef') {
      $dok = new Visning($this->cd);
	    $dok->vis('utvalg/vaktsjef/utvalg_vaktsjef.php');
    }
    else {
      $dok = new Visning($this->cd);
      $dok->vis('utvalg/vaktsjef/utvalg_vaktsjef.php');
    }
  }
}

?>
