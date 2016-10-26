<?php

namespace intern3;

class HelgaGjesteListe {

    public static function getGjesteListeByBeboerAar($beboer_id, $aar) {

        $st = DB::getDB()->prepare('SELECT * FROM helgagjest WHERE (vert=:vert AND aar=:aar)');
        $st->bindParam(':vert', $beboer_id);
        $st->bindParam(':aar',  $aar);
        $st->execute();

        $resultat = $st->fetchAll();

        $gjester = array();

        foreach( $resultat as $gjest ){
            $instansen = new HelgaGjest($gjest['id'], $gjest['epost'], $gjest['navn'], $gjest['vert'], $gjest['sendt_epost'], $gjest['inne']);
            $gjester[] = $instansen;
        }
        return $gjester;
    }

    public static function getAlleGjesterAar($aar){
        $st = DB::getDB()->prepare('SELECT * FROM helgagjest WHERE (aar=:aar)');
        $st->bindParam(':aar',  $aar);
        $st->execute();

        $resultat = $st->fetchAll();

        $gjester = array();

        foreach( $resultat as $gjest ){
            $instansen = new HelgaGjest($gjest['id'], $gjest['epost'], $gjest['navn'], $gjest['vert'], $gjest['sendt_epost'], $gjest['inne']);
            $gjester[] = $instansen;
        }
        return $gjester;
    }

    public static function getGjesteCount($aar){
        return count(self::getAlleGjesterAar($aar));
    }

    public static function getGjesteCountBeboer($beboerid,$aar){
        return count(self::getGjesteListeByBeboerAar($beboerid, $aar));
    }


}

