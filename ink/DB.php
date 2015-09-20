<?php

namespace intern3;

class DB extends \PDO {
	private static $__instance = null;
	public static function getDB() {
		if (self::$__instance == null) {
			self::$__instance = new self();
		}
		return self::$__instance;
	}
	public function __construct() {
		// vi har en tulledatabase for utvikling: bruk den unntatt når vi er den offisielle internsida:
		$tullesuffiks = ($_SERVER['SERVER_NAME'] == 'intern.singsaker.no') ? '' : '_dev';

		$domene     = 'mysql:host=localhost;dbname=intern3' . $tullesuffiks;
		$brukernavn = 'intern3' . $tullesuffiks;
		$passord    = 'intern3';
		// hvis ikke my.cnf har [client].default-character-set=utf8:
		parent::__construct($domene, $brukernavn, $passord, array(\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
		parent::setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_WARNING);
	}
}

?>
