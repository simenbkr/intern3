<?php

namespace intern3;

class CtrlData {
	private $arg;
	private $pos;
	private $aktivBruker;
	private $base;
	public function __construct($arg, $pos = 0) {
		$this->arg = (array) $arg;
		$this->pos = $pos;
		$this->aktivBruker = null;
		$this->base = array();
	}
	public function getArg($pos) {
		return isset($this->arg[$pos]) ? $this->arg[$pos] : null;
	}
	public function getAktuellArgPos() {
		$len = count($this->arg);
		return $len > $this->pos ? $this->pos : -1;
	}
	public function getAktueltArg() {
		$pos = $this->getAktuellArgPos();
		return $pos == -1 ? null : $this->arg[$pos];
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
	public function getBase($pos = 0) {
		if (!isset($this->base[$pos])) {
			if ($pos == 0) {
				$pos = $this->getAktuellArgPos();
			}
			else if (count($this->arg) <= $pos) {
				return '';
			}
			$this->base[$pos] = implode('/', array_slice($this->arg, 0, $pos));
		}
		return $this->base[$pos];
	}
	//TODO: adminBruker (for å logge inn som andre brukere)
}

?>