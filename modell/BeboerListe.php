<?php

namespace intern3;

class BeboerListe
{
    public static function alle()
    {
        $st = DB::getDB()->prepare('SELECT id FROM beboer ORDER BY fornavn,mellomnavn,etternavn COLLATE utf8_swedish_ci;');
        return self::medPdoSt($st);
    }

    public static function aktive()
    {
        // Dette er ikke noen god måte å luke ut perm og utflytting på sikt.
        // Forvent gjerne at dette feiler en dag.
        $ikkeUtflyttet = '%"utflyttet":NULL%';
        $st = DB::getDB()->prepare('SELECT id FROM beboer WHERE romhistorikk LIKE :ikkeUtflyttet ORDER BY fornavn,mellomnavn,etternavn COLLATE utf8_swedish_ci;');
        $st->bindParam(':ikkeUtflyttet', $ikkeUtflyttet);
        return self::medPdoSt($st);
    }

    public static function ikkeAktive()
    {
        $aktive = self::aktive();
        $alle = self::alle();
        $ikke_aktive = array();
        foreach ($alle as $beboer) {
            if (!in_array($beboer, $aktive)) {
                $ikke_aktive[] = $beboer;
            }
        }
        return $ikke_aktive;
    }

    public static function aktiveMedAlko()
    {
        $lista = array();
        foreach (self::aktive() as $beboer) {
            if ($beboer->harAlkoholdepositum()) {
                $lista[] = $beboer;
            }
        }
        return $lista;
    }

    public static function reseppListe()
    {
        $ikkeUtflyttet = '%"utflyttet":NULL%';
        $st = DB::getDB()->prepare("select id from beboer where (alkoholdepositum=1 and romhistorikk LIKE :ikkeUtflyttet) 
and id in 
(select beboerId from prefs where resepp=1)");
        $st->bindParam(':ikkeUtflyttet', $ikkeUtflyttet);
        return self::medPdoSt($st);
    }

    public static function vinkjellerListe()
    {
        //SQL-ninja shit yo
        $ikkeUtflyttet = '%"utflyttet":NULL%';
        $st = DB::getDB()->prepare("select id from beboer where (alkoholdepositum=1 and romhistorikk LIKE :ikkeUtflyttet) 
and id in 
(select beboerId from prefs where vinkjeller=1)");
        $st->bindParam(':ikkeUtflyttet', $ikkeUtflyttet);
        return self::medPdoSt($st);
    }

    public static function aktiveMedRegi()
    {
        $lista = array();
        foreach (self::aktive() as $beboer) {
            if ($beboer->getRolle()->getRegitimer() > 0) {
                $lista[] = $beboer;
            }
        }
        return $lista;
    }

    public static function aktiveMedRegiTilDisp()
    {
        $lista = array();
        foreach (self::aktiveMedRegi() as $beboer) {
            /* @var Beboer $beboer */
            if (!$beboer->harUtvalgVerv() && $beboer->getBruker()->getDisponibelRegitid() > 0) {
                $lista[] = $beboer;
            }
        }
        return $lista;
    }

    public static function aktiveMedRegitimer($regitimer)
    {
        $lista = array();
        $semester = Funk::generateSemesterString(date('Y-m-d'));
        foreach (self::aktiveMedRegi() as $beboer) {
            /* @var \intern3\Beboer $beboer */
            $antallSek = $beboer->getRolle()->getRegitimer() * 3600;
            if ($antallSek - $beboer->getBruker()->getRegisekunderMedSemester($semester) < $regitimer * 3600) {
                $lista[] = $beboer;
            }
        }

        return $lista;
    }

    public static function medBursdag($dato)
    {
        $ikkeUtflyttet = '%"utflyttet":NULL%';
        $bursdag = '%-' . $dato;
        $st = DB::getDB()->prepare('SELECT id FROM beboer WHERE romhistorikk LIKE :ikkeUtflyttet AND fodselsdato LIKE :bursdag ORDER BY fodselsdato;');
        $st->bindParam(':ikkeUtflyttet', $ikkeUtflyttet);
        $st->bindParam(':bursdag', $bursdag);
        return self::medPdoSt($st);
    }

    public static function medVervId($vervId)
    {
        $st = DB::getDB()->prepare('SELECT b.id AS id FROM
	beboer AS b,beboer_verv AS v
WHERE
	b.id=v.beboer_id
	AND v.verv_id=:vervId
ORDER BY fornavn,mellomnavn,etternavn COLLATE utf8_swedish_ci;');
        $st->bindParam(':vervId', $vervId);
        return self::medPdoSt($st);
    }

    public static function utenVervId($vervId)
    {
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

    public static function harVakt()
    { // TODO burde sjekke rolle id = 1 eller 2 istedenfor regitimer..
        $ikkeUtflyttet = '%"utflyttet":NULL%';
        $st = DB::getDB()->prepare('SELECT b.id AS id FROM beboer AS b, rolle AS r WHERE b.rolle_id=r.id AND r.regitimer < 48 AND b.romhistorikk LIKE :ikkeUtflyttet ORDER BY fornavn,mellomnavn,etternavn COLLATE utf8_swedish_ci;');
        $st->bindParam(':ikkeUtflyttet', $ikkeUtflyttet);
        return self::medPdoSt($st);
    }

    public static function fullVakt()
    {
        $ikkeUtflyttet = '%"utflyttet":NULL%';
        $st = DB::getDB()->prepare('SELECT b.id AS id FROM beboer AS b, rolle AS r WHERE b.rolle_id=r.id AND r.regitimer = 0 AND b.romhistorikk LIKE :ikkeUtflyttet ORDER BY fornavn,mellomnavn,etternavn COLLATE utf8_swedish_ci;');
        $st->bindParam(':ikkeUtflyttet', $ikkeUtflyttet);
        return self::medPdoSt($st);
    }

    public static function halvVakt()
    {
        $ikkeUtflyttet = '%"utflyttet":NULL%';
        $st = DB::getDB()->prepare('SELECT b.id AS id FROM beboer AS b, rolle AS r WHERE b.rolle_id=r.id AND r.regitimer > 0 AND b.romhistorikk LIKE :ikkeUtflyttet ORDER BY fornavn,mellomnavn,etternavn COLLATE utf8_swedish_ci;');
        $st->bindParam(':ikkeUtflyttet', $ikkeUtflyttet);
        return self::medPdoSt($st);
    }

    public static function medPdoSt($st)
    {
        $st->execute();
        $res = array();
        while ($rad = $st->fetch()) {
            $res[] = Beboer::medId($rad['id']);
        }
        return $res;
    }

    public static function fraStorhybelliste($storhybel_id){
        $st = DB::getDB()->prepare('SELECT beboer_id FROM storhybel_velger WHERE storhybel_id=:id');
        $st->bindParam(':id', $storhybel_id);
        $st->execute();

        $lista = array();
        foreach($st->fetchAll() as $rad){
            $lista[$rad['beboer_id']] = Beboer::medId($rad['beboer_id']);
        }

        return $lista;

    }

    public static function singleStorhybelliste($storhybel_id) {

        /*
         * Det følger en litt kompleks SQL-spørring. Denne henter ut folk som ikke er oppført alene
         * på storhybellista med id $storhybel_id.
         *
         */

        $st = DB::getDB()->prepare('
        SELECT beboer_id FROM storhybel_velger WHERE 
        (velger_id NOT IN 
        (SELECT velger_id FROM storhybel_velger AS sv WHERE 
            (sv.storhybel_id = :id) 
            GROUP BY velger_id HAVING count(*) > 1)    
        AND storhybel_id=:id)
        ');
        $st->bindParam(':id', $storhybel_id);
        $st->execute();

        $lista = array();
        foreach($st->fetchAll() as $rad){
            $lista[$rad['beboer_id']] = Beboer::medId($rad['beboer_id']);
        }

        return $lista;
    }
}

?>
