<?php

namespace intern3;

class VervListe {
	public static function alle() {
		$st = DB::getDB()->prepare('SELECT id FROM verv ORDER BY navn COLLATE utf8_swedish_ci;');
		return self::medPdoSt($st);
	}
	public static function medPdoSt($st) {
		$st->execute();
		$res = array();
		while ($rad = $st->fetch()) {
			$res[] = Verv::medId($rad['id']);
		}
		return $res;
	}
}

?>