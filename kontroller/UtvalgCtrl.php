<?php

namespace intern3;

class UtvalgCtrl extends AbstraktCtrl {
	public function bestemHandling() {
    $aktivBruker = LogginnCtrl::getAktivBruker();
    if ($aktivBruker->getPerson()->harUtvalgVerv()) {
      $aktueltArg = $this->cd->getAktueltArg();
      switch ($aktueltArg) {
        case 'romsjef':
          $valgtCtrl = new RomsjefCtrl($this->cd->skiftArg());
          break;
        case 'regisjef':
          $valgtCtrl = new RegisjefCtrl($this->cd->skiftArg());
          break;
        case 'sekretar':
          $valgtCtrl = new SekretarCtrl($this->cd->skiftArg());
          break;
        case 'vaktsjef':
          $valgtCtrl = new VaktsjefCtrl($this->cd->skiftArg());
          break;
        case 'kosesjef':
          $valgtCtrl = new KosesjefCtrl($this->cd->skiftArg());
          break;
        default:
          $dok = new Visning($this->cd);
          $dok->vis('utvalg.php');
          break;
      }
      $valgtCtrl->bestemHandling();
    }
  }
}

?>
