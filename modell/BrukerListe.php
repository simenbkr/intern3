<?php

namespace intern3;

class BrukerListe {

	public static function harRegi () {
		$brukere = array();
		foreach (BeboerListe::aktive() as $beboer) {
			$brk = $beboer->getBruker();
			if ($brk == null || !$brk->getPerson()->erBeboer()) {
				continue;
			}
			$brukere[] = $brk;
		}
		return $brukere;
	}
	public static function harRegiIgjen ($unix = false) {
		$res = array();
		foreach (self::harRegi() as $bruker) {
			if ($bruker->getPerson()->getRolle()->getRegitimer() * 3600 > $bruker->getRegisekunderMedSemester($unix)) {
				$res[] = $bruker;
			}
		}
	return $res;
	}
	public static function harIkkeRegiIgjen ($unix = false) {
		$res = array();
		foreach (self::harRegi() as $bruker) {
			if ($bruker->getPerson()->getRolle()->getRegitimer() * 3600 <= $bruker->getRegisekunderMedSemester($unix)) {
				$res[] = $bruker;
			}
		}
	return $res;
	}

}

?>