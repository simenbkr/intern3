<?php

namespace intern3;

abstract class AbstraktCtrl {
	protected $cd;
	public function __construct(CtrlData $cd) {
		$this->cd = $cd;
	}
}

?>