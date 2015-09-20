<?php

namespace intern3;

class KryssCtrl extends AbstraktCtrl {
	public function bestemHandling() {
		$krysselisteListe = Krysseliste::medBeboerId($this->cd->getAktivBruker()->getPerson()->getId());
		$sumKryss = array();
		$ukedager = array(
				date('D', strtotime('this monday')) => 0,
				date('D', strtotime('this tuesday')) => 0,
				date('D', strtotime('this wednesday')) => 0,
				date('D', strtotime('this thursday')) => 0,
				date('D', strtotime('this friday')) => 0,
				date('D', strtotime('this saturday')) => 0,
				date('D', strtotime('this sunday')) => 0
		);
		foreach ($krysselisteListe as $krysseliste) {
			$antall = 0;
			foreach ($krysseliste->getKryssListe() as $kryss) {
				$antall += $kryss->antall;
				$ukedager[date('D', strtotime($kryss->tid))] += $kryss->antall;
			}
			$sumKryss[$krysseliste->getDrikke()->getNavn()] = $antall;
		}
		$sum = array_sum($ukedager);
		foreach ($ukedager as $dag => $antall) {
			$ukedager[$dag] = round(($antall / $sum) * 100);
		}
		$dok = new Visning($this->cd);
		$dok->set('sumKryss', $sumKryss);
		$dok->set('ukedager', $ukedager);
		$dok->vis('kryss.php');
	}
}

?>
