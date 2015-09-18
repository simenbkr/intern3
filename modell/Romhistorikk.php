<?php

// Sånn her må man gjøre for å få nøsta klasser:

namespace intern3\Romhistorikk;

class Periode {
	public $romId;
	public $innflyttet;
	public $utflyttet;

	public function __construct($romId, $innflyttet, $utflyttet) {
		$this->romId = $romId;
		$this->innflyttet = $innflyttet;
		$this->utflyttet = $utflyttet;
	}
}

namespace intern3;

class Romhistorikk {
	public $romHistorikk;

	public function __construct() {
		$this->romHistorikk = array();
	}

	public function addPeriode($romId, $innflyttet, $utflyttet) {
		$this->romHistorikk[] = new Romhistorikk\Periode($romId, $innflyttet, $utflyttet);
	}

	public function tilJson() {
		return json_encode($this->romHistorikk);
	}

	public static function fraJson($json) {
		$strukt = json_decode($json);
		$objekt = new self();
		foreach ($strukt as $periode) {
			$objekt->addPeriode(
					$periode->romId,
					$periode->innflyttet,
					$periode->utflyttet
			);
		}
		return $objekt;
	}

	public function getAktivRomId() {
		$len = count($this->romHistorikk);
		if ($len == 0) {
			return null;
		}
		return $this->romHistorikk[$len - 1]->romId;
	}

	public function getAktivtRom() {
		return Rom::medId($this->getAktivRomId());
	}

	public function getAntallSemestre() {
		$sum = 0;
		foreach ($this->romHistorikk as $periode) {
			$sum += ($this->getSemester(
					$periode->utflyttet == null ? date('Y-m-d', $_SERVER['REQUEST_TIME']) : $periode->utflyttet
			) - $this->getSemester($periode->innflyttet)) * 2;
		}
		return $sum;
	}

	private function getSemester($dato) {
		/* Gir årstall eller årstall + 0.5 hvis måned er etter juni. */
		// Passer ikke å ha her, kanskje heller i en DatoUtil-klasse e.l.
		$unix = strtotime($dato);
		$sem = substr($dato, 0, 4) + (date('n', $unix) > 6 ? .5 : 0);
		return $sem;
	}
}

?>