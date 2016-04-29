<?php

namespace intern3;

class RolleListe {
	public static function alle() {
		$st = DB::getDB()->prepare('SELECT id FROM rolle ORDER BY navn ASC;');
		return self::medPdoSt($st);
	}
	public static function medPdoSt($st) {
		$st->execute();
		$res = array();
		while ($rad = $st->fetch()) {
			$res[] = Rolle::medId($rad['id']);
		}
		return $res;
	}
}

?>
