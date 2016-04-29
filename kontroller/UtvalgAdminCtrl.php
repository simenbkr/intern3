<?php

namespace intern3;

class UtvalgAdminCtrl extends AbstraktCtrl {
	public function bestemHandling() {
		$bruker = Bruker::medId($this->cd->getAktueltArg());
		if ($bruker == null) {
			$dok = new Visning($this->cd);
			$dok->set('personListe', BeboerListe::aktive());
			$dok->vis('utvalg_admin.php');
		}
		else {
			$hovedCtrl = new HovedCtrl($this->cd->skiftArgMedRot($bruker));
			$hovedCtrl->bestemKontroller();
		}
	}
}

?>
