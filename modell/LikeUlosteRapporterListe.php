<?php

namespace intern3;

class LikeUlosteRapporterListe
{
	public static function medStedOgFeil($rom_id, $feil_id)
	{
		$db    = DB::getDB();
		$liste = array();

		$st = $db->prepare('SELECT rapport.id AS id FROM rapport, kvittering WHERE kvittering.id = rapport.kvittering_id AND kvittering.rom_id = :rom_id AND feil_id = :feil_id AND godkjent = 0');
		$st->bindParam(':rom_id',$rom_id);
		$st->bindParam(':feil_id',$feil_id);
		$st->execute();

		while($fk = $st->fetch())
		{
			$liste[] = Rapport::medId($fk['id']);
		}

		return $liste;
	}
}

?>
