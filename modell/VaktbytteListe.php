<?php

namespace intern3;

class VaktbytteListe {
	public static function etterVakttype() {
		return array(
				'1' => self::medVakttype(1),
				'2' => self::medVakttype(2),
				'3' => self::medVakttype(3),
				'4' => self::medVakttype(4)
		);
	}
	public static function medVakttype($vakttype) {
		$st = DB::getDB()->prepare('SELECT b.id AS id FROM
	vaktbytte AS b,
	vakt AS v
WHERE
	v.id=b.vakt_id
	AND v.vakttype=:vakttype
GROUP BY v.id
ORDER BY v.dato;');
		$st->bindParam(':vakttype', $vakttype);
		return self::medPdoSt($st);
	}
	public static function medPdoSt($st) {
		$st->execute();
		$res = array();
		while ($rad = $st->fetch()) {
			$res[] = Vaktbytte::medId($rad['id']);
		}
		return $res;
	}
}

?>