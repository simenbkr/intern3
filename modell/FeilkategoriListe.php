<?php

namespace intern3;

class FeilkategoriListe
{
	public static function alle()
	{
		$db = DB::getDB();
		$liste = array();

		$st = $db->prepare('SELECT id FROM feilkategori WHERE id != 0 ORDER BY navn ASC');
		$st->execute();

		while($fk = $st->fetch())
		{
			$liste[] = Feilkategori::medId($fk['id']);
		}

		return $liste;
	}
}

?>
