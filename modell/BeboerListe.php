<?php

namespace intern3;

class BeboerListe {
	public static function aktive() {
		// Dette er ikke noen god måte å luke ut perm og utflytting på sikt.
		// Forvent gjerne at dette feiler en dag.
		$ikkeUtflyttet = '%"utflyttet":NULL%';
		$st = DB::getDB()->prepare('SELECT id FROM beboer WHERE romhistorikk LIKE :ikkeUtflyttet ORDER BY fornavn COLLATE utf8_swedish_ci;');
		$st->bindParam(':ikkeUtflyttet', $ikkeUtflyttet);
		$st->execute();
		$beboerListe = array();
		while ($rad = $st->fetch()) {
			$beboerListe[] = Beboer::medId($rad['id']);
		}
		return $beboerListe;
	}
}

?>