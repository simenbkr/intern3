<?php

namespace intern3;

class RomListe extends Liste {
	public static function alle() {
		return self::listeFraSql('Rom::medId', 'SELECT id FROM rom ORDER BY navn ASC;');
	}
}

?>