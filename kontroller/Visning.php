<?php

namespace intern3;

class Visning {
	private $mal;
	private $var;
	public function __construct($mal) {
		if (!is_string($mal)) {
			throw new \Exception('Inndata må være en streng.');
		}
		$this->mal = $mal;
		$this->var = array();
	}
	public function set($nokkel, $verdi) {
		$this->var[$nokkel] = $verdi;
	}
	public function vis() {
		$sti = '../visning/' . $this->mal;
		if (!file_exists($sti)) {
			throw new \Exception('Kan ikke vise ' . $this->mal);
		}
		foreach ($this->var as $nokkel => $verdi) {
			${$nokkel} = $verdi;
		}
		require_once($sti);
	}
}

?>