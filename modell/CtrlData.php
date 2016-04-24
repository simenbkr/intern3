<?php

namespace intern3;

class CtrlData {
	private $arg;
	private $pos;
	private $aktivBruker;
	public function __construct($arg, $pos = 0) {
		$this->arg = (array) $arg;
		$this->pos = $pos;
		$this->aktivBruker = null;
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
		$kopi = new self($this->arg, $this->pos + 1);
		$kopi->setAktivBruker($this->aktivBruker);
		return $kopi;
	}
	public function setAktivBruker($aktivBruker) {
		$this->aktivBruker = $aktivBruker;
	}
	public function getAktivBruker() {
		return $this->aktivBruker;
	}
	//TODO: adminBruker (for å logge inn som andre brukere)
}

?>