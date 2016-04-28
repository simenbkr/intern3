<?php

namespace intern3;

class ArbeidskategoriListe
{
	private static function listeFraPdo($st) {
		$st->execute();
		$liste = array();
		while($rl = $st->fetch()) {
			$liste[] = Arbeidskategori::medId($rl['id']);
		}
		return $liste;
	}

	private static function listeFraSql($sql) {
		$db = DB::getDB();
		$st = $db->prepare($sql);
		return self::listeFraPdo($st);
	}

	/* Foreløpig er alle aktive.
	 * Man vil kanskje senere deaktivere en arbeidskategori,
	 * dvs ta den ut av syne uten å miste gamle data.
	 */
	public static function aktiveListe () {
		return self::listeFraSql('SELECT id FROM arbeidskategori;');
	}

}

?>
