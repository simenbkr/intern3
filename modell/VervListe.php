<?php

namespace intern3;

class VervListe
{
    public static function alle()
    {
        $st = DB::getDB()->prepare('SELECT id FROM verv ORDER BY navn COLLATE utf8_swedish_ci;');
        return self::medPdoSt($st);
    }

    public static function medBeboerId($beboerId)
    {
        $st = DB::getDB()->prepare('SELECT v.id AS id FROM
	verv AS v,
	beboer_verv AS b
WHERE
	v.id=b.verv_id
	AND b.beboer_id=:beboerId;');
        $st->bindParam(':beboerId', $beboerId);
        return self::medPdoSt($st);
    }

    public static function alleUtvalg()
    {
        $st = DB::getDB()->prepare('SELECT * from verv WHERE utvalg=1');
        return self::medPdoSt($st);
    }

    public
    static function utvalgMedBeboerId($beboerId)
    {
        $st = DB::getDB()->prepare('SELECT v.id AS id FROM
	verv AS v,
	beboer_verv AS b
WHERE
	v.id=b.verv_id
	AND b.beboer_id=:beboerId
	AND v.utvalg=1;');
        $st->bindParam(':beboerId', $beboerId);
        return self::medPdoSt($st);
    }

    public
    static function medPdoSt($st)
    {
        $st->execute();
        $res = array();
        while ($rad = $st->fetch()) {
            $res[] = Verv::medId($rad['id']);
        }
        return $res;
    }
}

?>