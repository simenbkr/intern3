<?php

namespace intern3;

class UtvalgRegisjefArbeidCtrl extends AbstraktCtrl {
	public function bestemHandling() {
		if (isset($_POST['id'])) {
			$this->godkjennArbeid($_POST['id'], @$_POST['underkjenn']);
		}
		$sideinndeling = new SideinndelData();
		$sideinndeling->setPerSide(200);
		$sideinndeling->setSide($this->cd->getAktueltArg());
		$dok = new Visning($this->cd);
		$dok->set('arbeidListe', ArbeidListe::alle($sideinndeling));
		$dok->set('sideinndeling', $sideinndeling);
		$dok->vis('utvalg_regisjef_arbeid.php');
	}
	private function godkjennArbeid($id, $underkjenn = '') {
		$arbeid = Arbeid::medId($id);
		if ($arbeid == null) {
			http_response_code(404);
			exit('Arbeid ble ikke funnet.');
		}
		$godkjent = $underkjenn == '' || $underkjenn == '0' ? 1 : 0;
		$godkjentBrukerId = $this->cd->getAktivBruker()->getId();
		$st = DB::getDB()->prepare('UPDATE arbeid SET godkjent=:godkjent,godkjent_bruker_id=:godkjent_bruker_id WHERE id=:id;');
		$st->bindParam(':godkjent', $godkjent);
		$st->bindParam(':godkjent_bruker_id', $godkjentBrukerId);
		$st->bindParam(':id', $id);
		$st->execute();
		$arbeid = Arbeid::medId($id);
		$dok = new Visning($this->cd);
		$dok->set('arbeid', $arbeid);
		$dok->vis('utvalg_regisjef_arbeid_rad.php');
		exit();
	}
}

?>
