<?php

namespace intern3\modell;

class Database extends PDO {

	public function __construct() {
		$tullesuffiks = ($_SERVER['SERVER_NAME'] == 'regiportal.singsaker.no') ? '' : '_dev';
		$domene     = 'mysql:host=localhost;dbname=intern3' . $tullesuffiks;
		$brukernavn = 'intern3$tullesuffiks';
		$passord    = 'intern3';
		parent::__construct($domene, $brukernavn, $passord);
		parent::setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
	}

}

?>
