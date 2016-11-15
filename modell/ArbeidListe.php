<?php

namespace intern3;

class ArbeidListe extends Liste
{

    public static function alle($Sideinndeling = null)
    {
        return self::listeFraSql('Arbeid::medId', 'SELECT id FROM arbeid ORDER BY godkjent ASC,tid_registrert DESC', array(), $Sideinndeling);
    }

    public static function alleSammen($Start = false, $Lengde = false)
    { // Forelda pga teit navn og feil parametre
        if (is_int($Lengde)) {
            if (!is_numeric($Start)) {
                $Start = 0;
            }
            $Grense = ' limit ' . $Start . ',' . $Lengde;
        } else {
            $Grense = '';
        }
        return self::listeFraSql('Arbeid::medId', 'SELECT id FROM arbeid order by godkjent,tid_registrert,id' . $Grense . ';', array());
    }

    public static function ikkeGodkjente($Sideinndeling = null)
    {
        return self::listeFraSql('Arbeid::medId', 'SELECT id FROM arbeid WHERE godkjent=0', array(), $Sideinndeling);
    }

    public static function medBrukerId($bruker_id, $Sideinndeling = null)
    {
        return self::listeFraSql('Arbeid::medId', 'SELECT arbeid.id FROM arbeid WHERE bruker_id=:brk;', array(':brk' => $bruker_id), $Sideinndeling);
    }

    public static function medSemester($unix = false, $Sideinndeling = null)
    {
        if ($unix === false) {
            global $_SERVER;
            $unix = $_SERVER['REQUEST_TIME'];
        }
        if (date('n', $unix) > 6) {
            $start = strtotime('first day of July', $unix);
            $slutt = strtotime('first day of January next year', $unix);
        } else {
            $start = strtotime('first day of January', $unix);
            $slutt = strtotime('first day of July', $unix);
        }
        $sql = 'SELECT arbeid.id FROM arbeid WHERE :dato_start <= tid_registrert and tid_registrert < :dato_slutt;';
        $param = array(':dato_start' => date('Y-m-d', $start),
            ':dato_slutt' => date('Y-m-d', $slutt));
        return self::listeFraSql('Arbeid::medId', $sql, $param, $Sideinndeling);
    }

    public static function medBrukerIdSemester($bruker_id, $unix = false, $Sideinndeling = null)
    {
        if ($unix === false) {
            global $_SERVER;
            $unix = $_SERVER['REQUEST_TIME'];
        }
        if (date('n', $unix) > 6) {
            $start = strtotime('first day of July', $unix);
            $slutt = strtotime('first day of January next year', $unix);
        } else {
            $start = strtotime('first day of January', $unix);
            $slutt = strtotime('first day of July', $unix);
        }
        $sql = 'SELECT arbeid.id FROM arbeid WHERE bruker_id=:brk and :dato_start <= tid_registrert and tid_registrert < :dato_slutt;';
        $param = array(':brk' => $bruker_id,
            ':dato_start' => date('Y-m-d', $start),
            ':dato_slutt' => date('Y-m-d', $slutt));
        return self::listeFraSql('Arbeid::medId', $sql, $param, $Sideinndeling);
    }

    public static function medOppgaveId($oppgave_id, $Sideinndeling = null)
    {
        $sql = 'SELECT arbeid.id FROM arbeid WHERE polymorfkategori_id=:oppg AND polymorfkategori_velger=:velg;';
        $velg = ArbeidPolymorfkategori::OPPG;
        $param = array(':oppg' => $oppgave_id,
            ':velg' => $velg);
        return self::listeFraSql('Arbeid::medId', $sql, $param, $Sideinndeling);
    }

    public static function medBrukerIdOppgIdVelg($brkId, $oppgId, $velg, $Sideinndeling = null)
    {
        $sql = 'SELECT arbeid.id FROM arbeid WHERE bruker_id=:brk AND polymorfkategori_id=:oppg AND polymorfkategori_velger=:velg;';
        $param = array(':brk' => $brkId,
            ':oppg' => $oppgId,
            ':velg' => $velg);
        return self::listeFraSql('Arbeid::medId', $sql, $param, $Sideinndeling);
    }

    public static function Antall()
    { // Denne trengs vel ikke når alleSammen() er borte og $Sideinndeling er på plass.
        $sql = 'select count(id) from arbeid;';
        $db = new DB();
        $st = $db->prepare($sql);
        $st->execute();
        $rl = $st->fetch();
        return $rl['count(id)'];
    }
}

?>
