<?php

namespace intern3;

class VervListe {
	public static function alle() {
		$st = DB::getDB()->prepare('SELECT id FROM verv ORDER BY navn COLLATE utf8_swedish_ci;');
		return self::medPdoSt($st);
	}
	public static function medPdoSt($st) {
		$st->execute();
		$beboerListe = array();
		while ($rad = $st->fetch()) {
			$beboerListe[] = Verv::medId($rad['id']);
		}
		return $beboerListe;
	}
}

?>