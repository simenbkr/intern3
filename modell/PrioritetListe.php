<?php

namespace intern3;

class PrioritetListe {
	public static function alle () {
		$db = DB::getDB();
		$liste = array();
		$st = $db->prepare('SELECT id FROM prioritet;');
		$st->execute();

		while($rl = $st->fetch())
		{
			$liste[] = Prioritet::medId($rl['id']);
		}

		return $liste;
	}
}

?>
