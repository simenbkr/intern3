<?php

namespace intern3;

class SkoleListe {
	public static function alle() {
		$st = DB::getDB()->prepare('SELECT id FROM skole ORDER BY navn ASC;');
		return self::medPdoSt($st);
	}
	public static function medPdoSt($st) {
		$st->execute();
		$res = array();
		while ($rad = $st->fetch()) {
			$res[] = Skole::medId($rad['id']);
		}
		return $res;
	}
}

?>
