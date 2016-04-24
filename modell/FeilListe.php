<?php

namespace intern3;

class FeilListe
{
	public static function medFeilkategori($id)
	{
		$db    = DB::getDB();
		$liste = array();

		$st = $db->prepare('SELECT id FROM feil WHERE feilkategori_id = :id');
		$st->bindParam(":id",$id);
		$st->execute();

		while($feil = $st->fetch())
		{
			$liste[] = Feil::medId($feil['id']);
		}

		return $liste;
	}
}

?>
