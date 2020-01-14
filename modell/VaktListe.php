<?php

namespace intern3;

class VaktListe
{
    public static function medDatoBrukerId($dato, $brukerId)
    {
        $st = DB::getDB()->prepare('SELECT id FROM vakt WHERE dato=:dato AND bruker_id=:brukerId ORDER BY vakttype;');
        $st->bindParam(':dato', $dato);
        $st->bindParam(':brukerId', $brukerId);
        return self::medPdoSt($st);
    }

    public static function medBrukerId($brukerId)
    {
        $st = DB::getDB()->prepare('SELECT id FROM vakt WHERE bruker_id=:brukerId ORDER BY dato, vakttype;');
        $st->bindParam(':brukerId', $brukerId);
        return self::medPdoSt($st);
    }

    public static function medBrukerIdAutogen($brukerId) {
        $st = DB::getDB()->prepare('SELECT id FROM vakt WHERE bruker_id=:brukerId AND autogenerert=1;');
        $st->bindParam(':brukerId', $brukerId);
        return self::medPdoSt($st);
    }

    public static function listeEtterDatoType($start_dato, $slutt_dato, $vakttype) {
        $st = DB::getDB()->prepare('SELECT id FROM vakt WHERE (bruker_id IN (NULL, 0) AND dato >= :start AND dato <= :slutt AND vakttype = :vakttype)');
        $st->execute(['start'=>$start_dato, 'slutt'=>$slutt_dato, 'vakttype'=>$vakttype]);
        return self::medPdoSt($st);
    }

    public static function autogenerert()
    {
        $st = DB::getDB()->prepare('SELECT id FROM vakt WHERE autogenerert=1;');
        return self::medPdoSt($st);
    }

    public static function medPdoSt($st)
    {
        $st->execute();
        $res = array();
        while ($rad = $st->fetch()) {
            $res[] = Vakt::medId($rad['id']);
        }
        return $res;
    }

    public static function autogenerertForstevakt()
    {
        $st = DB::getDB()->prepare('SELECT id FROM vakt WHERE (vakttype=1 AND autogenerert=1);');
        return self::medPdoSt($st);
    }

    public static function autogenerertVanligVakt()
    {
        $st = DB::getDB()->prepare('SELECT id FROM vakt WHERE (vakttype!=1 AND autogenerert=1);');
        return self::medPdoSt($st);
    }

    public static function autogenerertKjipVakt()
    {
        $st = DB::getDB()->prepare('SELECT id FROM vakt WHERE ( 
                                          (
                                            (DAYOFWEEK(dato) = 6 AND vakttype IN (3, 4) ) 
                                            OR (DAYOFWEEK(dato) = 7 AND vakttype IN (2,3,4) ) 
                                            OR (DAYOFWEEK(dato) = 1 AND vakttype IN (2))
                                            )
                                            AND autogenerert=1
                                          )');
        return self::medPdoSt($st);
    }

    public static function autogenerertIkkeKjipVakt()
    {
        $st = DB::getDB()->prepare('SELECT v.id FROM vakt as v WHERE v.id NOT IN (
											(SELECT id FROM vakt WHERE 
                                            (DAYOFWEEK(dato) = 6 AND vakttype IN (3, 4) ) 
                                            OR (DAYOFWEEK(dato) = 7 AND vakttype IN (2,3,4) ) 
                                            OR (DAYOFWEEK(dato) = 1 AND vakttype IN (2))
                                            OR vakttype = 1)
                                          ) AND autogenerert=1');
        return self::medPdoSt($st);
    }

    public static function medBrukerIdEtter($brukerid, $dato)
    {
        $st = DB::getDB()->prepare('SELECT id FROM vakt WHERE (bruker_id=:brukerId AND dato>:dato) ORDER BY dato, vakttype;');
        $st->bindParam(':brukerId', $brukerid);
        $st->bindParam(':dato', $dato);
        return self::medPdoSt($st);
    }

}