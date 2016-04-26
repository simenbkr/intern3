<?php

namespace intern3;

class AnsvarsomradeListe {
	public static function alle()
	{
		$db = new DB();
		$liste = array();

		$st = $db->prepare('SELECT id FROM ansvarsomrade ORDER BY navn ASC');
		$st->execute();

		while($ar = $st->fetch())
		{
			$liste[] = Ansvarsomrade::medId($ar['id']);
		}

		return $liste;
	}
}

?>