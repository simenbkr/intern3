<?php

namespace intern3;

class StudieListe {
	public static function alle() {
		$st = DB::getDB()->prepare('SELECT id FROM studie ORDER BY navn COLLATE utf8_swedish_ci;');
		return self::medPdoSt($st);
	}
	public static function medPdoSt($st) {
		$st->execute();
		$res = array();
		while ($rad = $st->fetch()) {
			$res[] = Studie::medId($rad['id']);
		}
		return $res;
	}
}

?>