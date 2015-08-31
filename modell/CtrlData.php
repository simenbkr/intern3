<?php

namespace intern3;

class CtrlData {
	private $sider;
	public function __construct($sider) {
		$this->sider = $sider;
	}
	public function getDenneSide() {
		$len = count($this->sider);
		return $len > 0 ? $this->sider[$len - 1] : null;
	}
	public function addSide($side) {
		$sider = $this->sider;
		array_push($sider, $side);
		return new self($sider);
	}
}

?>