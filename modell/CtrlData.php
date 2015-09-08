<?php

namespace intern3;

class CtrlData {
	private $arg;
	private $pos;
	public function __construct($arg, $pos = 0) {
		$this->arg = (array) $arg;
		$this->pos = $pos;
	}
	public function getAktueltArg() {
		$len = count($this->arg);
		return $len > $this->pos ? $this->arg[$this->pos] : null;
	}
	public function getSisteArg() {
		$len = count($this->arg);
		return $len > 0 ? $this->arg[$len - 1] : null;
	}
	public function skiftArg() {
		return new self($this->arg, $this->pos + 1);
	}
}

?>