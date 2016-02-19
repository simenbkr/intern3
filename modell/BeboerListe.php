<?php

namespace intern3;

class BeboerListe {
	public static function alle() {
		$st = DB::getDB()->prepare('SELECT id FROM beboer ORDER BY fornavn,mellomnavn,etternavn COLLATE utf8_swedish_ci;');
		return self::medPdoSt($st);
	}
	public static function aktive() {
		// Dette er ikke noen god måte å luke ut perm og utflytting på sikt.
		// Forvent gjerne at dette feiler en dag.
		$ikkeUtflyttet = '%"utflyttet":NULL%';
		$st = DB::getDB()->prepare('SELECT id FROM beboer WHERE romhistorikk LIKE :ikkeUtflyttet ORDER BY fornavn,mellomnavn,etternavn COLLATE utf8_swedish_ci;');
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
	public static function medVervId($vervId) {
		$st = DB::getDB()->prepare('SELECT b.id AS id FROM
	beboer AS b,beboer_verv AS v
WHERE
	b.id=v.beboer_id
	AND v.verv_id=:vervId
ORDER BY fornavn,mellomnavn,etternavn COLLATE utf8_swedish_ci;');
		$st->bindParam(':vervId', $vervId);
		return self::medPdoSt($st);
	}
	public static function utenVervId($vervId) {
		$ikkeUtflyttet = '%"utflyttet":NULL%';
		$st = DB::getDB()->prepare('SELECT id from beboer
WHERE
  id NOT IN (
    SELECT b.id AS id FROM
    	beboer AS b,beboer_verv AS v
    WHERE
    	b.id=v.beboer_id
      AND v.verv_id=:vervId
  )
  AND romhistorikk LIKE :ikkeUtflyttet
ORDER BY fornavn,mellomnavn,etternavn COLLATE utf8_swedish_ci;');
		$st->bindParam(':vervId', $vervId);
		$st->bindParam(':ikkeUtflyttet', $ikkeUtflyttet);
		return self::medPdoSt($st);
	}

  public static function harVakt() {
    $ikkeUtflyttet = '%"utflyttet":NULL%';
    $st = DB::getDB()->prepare('SELECT b.id AS id FROM beboer AS b, rolle AS r WHERE b.rolle_id=r.id AND r.regitimer < 48 AND b.romhistorikk LIKE :ikkeUtflyttet ORDER BY fornavn,mellomnavn,etternavn COLLATE utf8_swedish_ci;');
		$st->bindParam(':ikkeUtflyttet', $ikkeUtflyttet);
    return self::medPdoSt($st);
  }

	public static function medPdoSt($st) {
		$st->execute();
		$res = array();
		while ($rad = $st->fetch()) {
			$res[] = Beboer::medId($rad['id']);
		}
		return $res;
	}
}

?>
