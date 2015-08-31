<?php

namespace intern3;

class CtrlData {
	private $arg;
	public function __construct($arg) {
		$this->arg = (array) $arg;
	}
	public function getForsteArg() {
		$len = count($this->arg);
		return $len > 0 ? $this->arg[0] : null;
	}
	public function getSisteArg() {
		$len = count($this->arg);
		return $len > 0 ? $this->arg[$len - 1] : null;
	}
	public function addArg($nyttArg) {
		$arg = $this->arg;
		array_push($arg, $nyttArg);
		return new self($arg);
	}
}

?>