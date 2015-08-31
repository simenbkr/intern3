<?php

namespace intern3;

class Visning extends AbstraktCtrl {
	private $var;
	public function __construct(CtrlData $cd) {
		parent::__construct($cd);
		$this->var = array();
	}
	public function set($nokkel, $verdi) {
		$this->var[$nokkel] = $verdi;
	}
	public function vis($mal) {
		if (!is_string($mal)) {
			throw new \Exception('Inndata må være en streng.');
		}
		$sti = '../visning/' . $mal;
		if (!file_exists($sti)) {
			throw new \Exception('Kan ikke vise ' . $mal);
		}
		$cd = $this->cd;
		foreach ($this->var as $nokkel => $verdi) {
			${$nokkel} = $verdi;
		}
		require_once($sti);
	}
}

?>