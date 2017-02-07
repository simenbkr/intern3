<?php

namespace intern3;

class HelgaGjesteListe {

    public static function getGjesteListeByBeboerAar($beboer_id, $aar) {

        $st = DB::getDB()->prepare('SELECT * FROM helgagjest WHERE (vert=:vert AND aar=:aar)');
        $st->bindParam(':vert', $beboer_id);
        $st->bindParam(':aar',  $aar);
        $st->execute();

        $gjester = array();

        for($i = 0; $i < $st->rowCount(); $i++){
            $gjester[] = HelgaGjest::init($st);
        }
        return $gjester;
    }

    public static function getGjesteListeDagByBeboerAar($dag, $beboer_id, $aar) {

        $st = DB::getDB()->prepare('SELECT * FROM helgagjest WHERE (vert=:vert AND aar=:aar AND dag=:dag)');
        $st->bindParam(':vert', $beboer_id);
        $st->bindParam(':aar',  $aar);
        $st->bindParam(':dag', $dag);
        $st->execute();

        $gjester = array();
        for($i = 0; $i < $st->rowCount(); $i++){
            $gjester[] = HelgaGjest::init($st);
        }
        return $gjester;
    }


    public static function getAlleGjesterAar($aar){
        $st = DB::getDB()->prepare('SELECT * FROM helgagjest WHERE (aar=:aar)');
        $st->bindParam(':aar',  $aar);
        $st->execute();

        $gjester = array();

        for($i = 0; $i < $st->rowCount(); $i++){
            $gjester[] = HelgaGjest::init($st);
        }
        return $gjester;
    }

    public static function getGjesterUngrouped($aar, $dag){
        $st = DB::getDB()->prepare('SELECT * FROM helgagjest WHERE (aar=:aar AND dag=:dag)');
        $st->bindParam(':aar', $aar);
        $st->bindParam(':dag', $dag);
        $st->execute();
        $gjester = array();
        for($i = 0; $i < $st->rowCount(); $i++){
            $gjester[] = HelgaGjest::init($st);
        }
        return $gjester;
    }

    public static function getGjesterGroupedbyHost($aar, $dag){
        //dag er et tall mellom 0 og 2 og representerer hhv torsdag, fredag og lørdag.

        $st = DB::getDB()->prepare('SELECT * FROM helgagjest WHERE (aar=:aar AND dag=:dag) ORDER BY vert ASC');
        $st->bindParam(':aar', $aar);
        $st->bindParam(':dag', $dag);
        $st->execute();
        $gjestene = $st->fetchAll();
        $gjesteListen = array();

        foreach($gjestene as $gjest){
            $gjest = HelgaGjest::byRow($gjest);
            if(!isset($gjesteListen[$gjest->getVertId()])){
                $gjesteListen[$gjest->getVertId()] = array($gjest);
            }
            else {
                $gjesteListen[$gjest->getVertId()][] = $gjest;
            }
        }
        return $gjesteListen;
    }

    public static function getGjesteCount($aar){
        return count(self::getAlleGjesterAar($aar));
    }

    public static function getGjesteCountBeboer($beboerid,$aar){
        return count(self::getGjesteListeByBeboerAar($beboerid, $aar));
    }

    public static function getGjesteCountDagBeboer($dag, $beboerid,$aar){
        return count(self::getGjesteListeDagByBeboerAar($dag, $beboerid, $aar));
    }
}

