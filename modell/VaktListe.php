<?php

namespace intern3;

class VaktListe {
	public static function medDatoBrukerId($dato, $brukerId) {
		$st = DB::getDB()->prepare('SELECT id FROM vakt WHERE dato=:dato AND bruker_id=:brukerId ORDER BY vakttype;');
		$st->bindParam(':dato', $dato);
		$st->bindParam(':brukerId', $brukerId);
		return self::medPdoSt($st);
	}
	public static function medBrukerId($brukerId) {
		$st = DB::getDB()->prepare('SELECT id FROM vakt WHERE bruker_id=:brukerId ORDER BY dato, vakttype;');
		$st->bindParam(':brukerId', $brukerId);
		return self::medPdoSt($st);
	}
	public static function autogenerert() {
		$st = DB::getDB()->prepare('SELECT id FROM vakt WHERE autogenerert=1;');
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

	public static function autogenerertForstevakt(){
        $st = DB::getDB()->prepare('SELECT id FROM vakt WHERE (vakttype=1 AND autogenerert=1);');
        return self::medPdoSt($st);
	}

	public static function autogenerertVanligVakt(){
        $st = DB::getDB()->prepare('SELECT id FROM vakt WHERE (vakttype!=1 AND autogenerert=1);');
        return self::medPdoSt($st);
	}

	public static function medBrukerIdEtter($brukerid, $dato){
        $st = DB::getDB()->prepare('SELECT id FROM vakt WHERE (bruker_id=:brukerId AND dato>:dato) ORDER BY dato, vakttype;');
        $st->bindParam(':brukerId', $brukerid);
        $st->bindParam(':dato', $dato);
        return self::medPdoSt($st);
	}

}

?>
