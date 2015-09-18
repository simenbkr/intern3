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
}

?>