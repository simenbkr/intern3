<?php

namespace intern3;

class BeboerListe {
	public static function aktive() {
		// Dette er ikke noen god måte å luke ut perm og utflytting på sikt.
		// Forvent gjerne at dette feiler en dag.
		$ikkeUtflyttet = '%"utflyttet":NULL%';
		$st = DB::getDB()->prepare('SELECT id FROM beboer WHERE romhistorikk LIKE :ikkeUtflyttet ORDER BY fornavn COLLATE utf8_swedish_ci;');
		$st->bindParam(':ikkeUtflyttet', $ikkeUtflyttet);
		return self::medPdoSt($st);
	}
	public static function medBursdag($dato) {
		$ikkeUtflyttet = '%"utflyttet":NULL%';
		$bursdag = '%-' . $dato;
		$st = DB::getDB()->prepare('SELECT id FROM beboer WHERE romhistorikk LIKE :ikkeUtflyttet AND fodselsdato LIKE :bursdag ORDER BY fodselsdato;');
		$st->bindParam(':ikkeUtflyttet', $ikkeUtflyttet);
		$st->bindParam(':bursdag', $bursdag);
		return self::medPdoSt($st);
	}
	public static function medPdoSt($st) {
		$st->execute();
		$beboerListe = array();
		while ($rad = $st->fetch()) {
			$beboerListe[] = Beboer::medId($rad['id']);
		}
		return $beboerListe;
	}
}

?>