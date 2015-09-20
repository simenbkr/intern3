<?php

namespace intern3;

class VaktListe {
	public static function medDatoBrukerId($dato, $brukerId) {
		$st = DB::getDB()->prepare('SELECT id FROM vakt WHERE dato=:dato AND bruker_id=:brukerId ORDER BY vakttype;');
		$st->bindParam(':dato', $dato);
		$st->bindParam(':brukerId', $brukerId);
		return self::medPdoSt($st);
	}
	public static function medPdoSt($st) {
		$st->execute();
		$res = array();
		while ($rad = $st->fetch()) {
			$res[] = Vakt::medId($rad['id']);
		}
		return $res;
	}
}

?>