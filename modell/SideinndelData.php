<?php

namespace intern3;

class SideinndelData {
private $PerSide;
private $Side;
private $Antall;
private $Sider;
private $Start;
	public function __construct () {
	$this->PerSide = null;
	$this->Side = null;
	$this->Antall = null;
	$this->Sider = null;
	$this->Start = null;
	}
	public function setPerSide ($PerSide) {
	$this->PerSide = intval($PerSide);
	$this->Sider = null;
	}
	public function setSide ($Side) {
	$this->Side = intval($Side);
	$this->Sider = null;
	}
	public function setAntall ($Antall) {
	$this->Antall = intval($Antall);
	$this->Sider = null;
	}
	public function getPerSide () {
		if ($this->Sider == null) {
		$this->BeregnInndeling();
		}
	return intval($this->PerSide);
	}
	public function getSide () {
		if ($this->Sider == null) {
		$this->BeregnInndeling();
		}
	return max(1 , intval($this->Side));
	}
	public function getAntall () {
		if ($this->Sider == null) {
		$this->BeregnInndeling();
		}
	return intval($this->Antall);
	}
	public function getSider () {
		if ($this->Sider == null) {
		$this->BeregnInndeling();
		}
	return max(1 , intval($this->Sider));
	}
	public function getStart () {
		if ($this->Sider == null) {
		$this->BeregnInndeling();
		}
	return intval($this->Start);
	}
	private function BeregnInndeling () {
		if ($this->Antall == null) {
		return;
		}
		if ($this->Antall > 0) {
		$this->Sider = ceil($this->Antall / $this->PerSide);
			if (is_numeric($this->Side)) {
				if ($this->Side < 1 or $this->Side > $this->Sider) {
				$this->Side = $this->Sider;
				}
			}
			else {
			$this->Side = $this->Sider;
			}
		$this->Start = ($this->Sider - $this->Side) * $this->PerSide;
		}
		else {
		$this->Side = 1;
		$this->Sider = 1;
		$this->Start = 0;
		}
	//var_dump($this->PerSide , $this->Antall , $this->Side , $this->Sider , $this->Start);
	}
}

?>