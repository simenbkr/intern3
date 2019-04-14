<?php

namespace intern3;

require_once(__DIR__ . '/../config.php');

class DB extends \PDO {
	private static $__instance = null;
	public static function getDB() {
		if (self::$__instance == null) {
			self::$__instance = new self();
		}
		return self::$__instance;
	}
	public function __construct() {
		$domene     = 'mysql:host=' . DB_DOMAIN . ';dbname=' . DB_NAME . ';';
		$brukernavn = DB_USER;
		$passord    = DB_PW;
		// hvis ikke my.cnf har [client].default-character-set=utf8:
		parent::__construct($domene, $brukernavn, $passord, array(\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'utf8\''));
		parent::setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
	}

}

?>
