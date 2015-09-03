<?php

// Sånn her må man gjøre for å få nøsta klasser:

namespace intern3\Romhistorikk;

class Periode {
	public $rom_id;
	public $innflyttet;
	public $utflyttet;
	public function __construct($rom_id, $innflyttet, $utflyttet) {
		$this->rom_id = $rom_id;
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
	public function addPeriode($rom_id, $innflyttet, $utflyttet) {
		$this->romHistorikk[] = new Romhistorikk\Periode($rom_id, $innflyttet, $utflyttet);
	}
	public function toJson() {
		return json_encode($this->romHistorikk);
	}
}

?>