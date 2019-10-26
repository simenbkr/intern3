<?php

namespace intern3;

class Vakt
{

    private $id;
    private $brukerId;
    private $vakttype;
    private $dato;
    private $bytte;
    private $bekreftet;
    private $autogenerert;
    private $dobbelvakt;
    private $straffevakt;

    // Latskap
    private $bruker;
    private $vaktbytte;

    private $vaktbytteDenneErMedI;

    public static function medId($id)
    {
        $st = DB::getDB()->prepare('SELECT * FROM vakt WHERE id=:id;');
        $st->bindParam(':id', $id);
        $st->execute();
        return self::init($st);
    }

    public static function medDatoVakttype($dato, $vakttype)
    {
        $st = DB::getDB()->prepare('SELECT * FROM vakt WHERE dato=:dato AND vakttype=:vakttype;');
        $st->bindParam(':dato', $dato);
        $st->bindParam(':vakttype', $vakttype);
        $st->execute();
        return self::init($st);
    }

    private static function init(\PDOStatement $st)
    {
        $rad = $st->fetch();
        if ($rad == null) {
            return null;
        }
        $instance = new self();
        $instance->id = $rad['id'];
        $instance->brukerId = $rad['bruker_id'];
        $instance->vakttype = $rad['vakttype'];
        $instance->dato = $rad['dato'];
        $instance->bytte = $rad['bytte'] == 1 ? true : false;
        $instance->bekreftet = $rad['bekreftet'];
        $instance->autogenerert = $rad['autogenerert'];
        $instance->dobbelvakt = $rad['dobbelvakt'];
        $instance->straffevakt = $rad['straffevakt'];
        $instance->vaktbytteDenneErMedI = explode(',', $rad['vaktbytte_id']);
        return $instance;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getBytte()
    {
        return $this->bytte == 1;
    }

    public function getBrukerId()
    {
        return $this->brukerId;
    }

    public function getBruker()
    {
        if ($this->bruker == null) {
            $this->bruker = Bruker::medId($this->brukerId);
        }
        return $this->bruker;
    }

    public function getVakttype()
    {
        return $this->vakttype;
    }

    public function getDato()
    {
        return $this->dato;
    }

    public function getBekreftet()
    {
        return $this->bekreftet;
    }

    public function erLedig()
    {
        return $this->brukerId == 0 && $this->autogenerert;
    }

    public function getVaktbytte()
    {
        if ($this->vaktbytte == null) {
            $this->vaktbytte = Vaktbytte::medVaktId($this->id);
        }
        return $this->vaktbytte;
    }

    public function vilBytte()
    {
        return $this->getVaktbytte() <> null;
    }

    public function getVaktbytteDenneErMedI()
    {
        return $this->vaktbytteDenneErMedI;
    }

    public function erForeslatt()
    {
        $st = DB::getDB()->prepare('SELECT id FROM vaktbytte');
        $st->execute();
        $rows = $st->fetchAll();
        foreach ($rows as $row) {
            $instans = Vaktbytte::medId($row['id']);
            if (count($instans->getForslagIder()) > 0 && in_array($this->id, $instans->getForslagIder())) {
                return true;
            }
        }
        return false;
    }

    public function erFerdig()
    {
        $tid = strtotime($this->getDato());
        $tid = strtotime('midnight', $tid);
        switch ($this->getVakttype()) {
            case '1':
                $tid = strtotime('+8 hour', $tid);
                break;
            case '2':
                $tid = strtotime('+13 hour', $tid);
                break;
            case '3':
                $tid = strtotime('+19 hour', $tid);
                break;
            case '4':
                $tid = strtotime('+25 hour', $tid);
                break;
        }
        return $tid <= $_SERVER['REQUEST_TIME'];
    }

    public function getVaktbytteDenneErMedIId()
    {
        //Array med ider f.eks array(1,2,3,).
        return is_array($this->vaktbytteDenneErMedI) ? $this->vaktbytteDenneErMedI : array();
    }

    public function slettVaktbytteIdFraInstans($vaktbytte_id)
    {
        if (in_array($vaktbytte_id, $this->getVaktbytteDenneErMedIId())) {
            $nytt_vaktbytte_med_i = array();
            foreach ($this->getVaktbytteDenneErMedIId() as $id) {
                if ($id != $vaktbytte_id) {
                    $nytt_vaktbytte_med_i[] = $id;
                }
            }
            if (count($nytt_vaktbytte_med_i) <= 0) {
                $nytt_vaktbytte_med_i = null;
            } else {
                $nytt_vaktbytte_med_i = implode(',', array_filter($nytt_vaktbytte_med_i));
            }

            if ($nytt_vaktbytte_med_i) {
                $st = DB::getDB()->prepare('UPDATE vakt SET vaktbytte_id=:nytt_vaktbytte WHERE id=:id');
                $st->bindParam(':nytt_vaktbytte', $nytt_vaktbytte_med_i);
                $st->bindParam(':id', $this->id);
                $st->execute();
            } else {
                $st = DB::getDB()->prepare('UPDATE vakt SET vaktbytte_id=:nytt_vaktbytte,bytte=NULL WHERE id=:id');
                $st->bindParam(':nytt_vaktbytte', $nytt_vaktbytte_med_i);
                $st->bindParam(':id', $this->id);
                $st->execute();
            }
        }
    }

    public function toString()
    {
        $df = new \IntlDateFormatter('nb_NO',
            \IntlDateFormatter::TRADITIONAL, \IntlDateFormatter::NONE,
            'Europe/Oslo');
        return $this->getVakttype() . ". vakt " . $df->format(strtotime($this->getDato()));
    }

    public function shortToString()
    {
        $df = new \IntlDateFormatter('nb_NO',
            \IntlDateFormatter::FULL, \IntlDateFormatter::NONE,
            'Europe/Oslo');

        return ucfirst($df->format(strtotime($this->getDato())));
    }

    public function medToString()
    {
        $df = new \IntlDateFormatter('nb_NO',
            \IntlDateFormatter::FULL, \IntlDateFormatter::NONE,
            'Europe/Oslo');

        return $this->vakttype . '. vakt ' . ucfirst($df->format(strtotime($this->getDato())));
    }

    public static function antallVakter()
    {
        $st = DB::getDB()->prepare('SELECT count(id) AS antall FROM vakt');
        $st->execute();
        $res = $st->fetch();
        return $res['antall'];
    }

    public static function antallUfordelte()
    {
        $st = DB::getDB()->prepare('SELECT count(id) AS antall FROM vakt WHERE bruker_id = 0;');
        $st->execute();
        $res = $st->fetch();
        return $res['antall'];
    }

    public static function antallUbekreftet()
    {
        $st = DB::getDB()->prepare('SELECT count(id) AS antall FROM vakt WHERE (bekreftet = 0 AND bruker_id != 0)');
        $st->execute();
        $res = $st->fetch();
        return $res['antall'];
    }

    public static function antallSkalSitteMedBrukerId($brukerId)
    {

        $semester = Funk::generateSemesterString(date('Y-m-d'));

        if (($vaktantall = VaktAntall::medIdSemester($brukerId, $semester)) != null) {
            return $vaktantall->getAntall();
        }


        $beboer = Beboer::medBrukerId($brukerId);
        if ($beboer == null || !$beboer->erBeboer()) {
            return 0;
        }
        $rolle = $beboer->getRolle();
        return date('m') > 6 ? $rolle->getVakterH() : $rolle->getVakterV();
    }

    public static function antallHarSittetMedBrukerId($brukerId)
    {
        $st = DB::getDB()->prepare('SELECT count(id) AS antall FROM vakt WHERE bruker_id=:brukerId AND vakt.dato < CURDATE();');
        $st->bindParam(':brukerId', $brukerId);
        $st->execute();
        $res = $st->fetch();
        return $res['antall'];
    }

    public static function antallErOppsattMedBrukerId($brukerId)
    {
        $st = DB::getDB()->prepare('SELECT count(id) AS antall FROM vakt WHERE bruker_id=:brukerId AND vakt.dato >= CURDATE()');
        $st->bindParam(':brukerId', $brukerId);
        $st->execute();
        $res = $st->fetch();
        return $res['antall'];
    }

    public static function antallForsteMedBrukerId($brukerId)
    {
        $st = DB::getDB()->prepare('SELECT count(id) AS antall FROM vakt WHERE bruker_id=:brukerId AND vakttype=\'1\'');
        $st->bindParam(':brukerId', $brukerId);
        $st->execute();
        $res = $st->fetch();
        return $res['antall'];
    }

    public static function antallStraffeVakter($brukerId)
    {
        $st = DB::getDB()->prepare('SELECT id FROM vakt WHERE bruker_id=:bruker_id AND straffevakt=1');
        $st->bindParam(':bruker_id', $brukerId);
        $st->execute();
        return $st->rowCount();
    }

    public static function antallDobbelVakter($brukerId)
    {
        $st = DB::getDB()->prepare('SELECT id FROM vakt WHERE bruker_id=:bruker_id AND dobbelvakt=1');
        $st->bindParam(':bruker_id', $brukerId);
        $st->execute();
        return $st->rowCount();
    }


    public static function antallHarIgjenMedBrukerId($brukerId, $skalSitte)
    {
        $antall = $skalSitte;
        $antall -= Vakt::antallHarSittetMedBrukerId($brukerId);
        //$antall += self::antallStraffeVakter($brukerId);
        return $antall;
    }

    public static function antallIkkeOppsattMedBrukerId($brukerId, $skalSitte)
    {
        $antall = $skalSitte;
        $antall -= Vakt::antallHarSittetMedBrukerId($brukerId);
        $antall -= Vakt::antallErOppsattMedBrukerId($brukerId);
        return $antall;
    }

    public static function antallIkkeBekreftetMedBrukerId($brukerId)
    {
        $st = DB::getDB()->prepare('SELECT count(id) AS antall FROM vakt WHERE bruker_id=:brukerId AND bekreftet = 0');
        $st->bindParam(':brukerId', $brukerId);
        $st->execute();
        $res = $st->fetch();
        return $res['antall'];
    }

    public static function settVakt($brukerId, $vaktId)
    {
        $st = DB::getDB()->prepare('UPDATE vakt SET bruker_id=:bruker_id WHERE id=:id');
        $st->bindParam(':bruker_id', $brukerId);
        $st->bindParam(':id', $vaktId);
        $st->execute();
    }

    public static function lagVakt($vakttype, $dato)
    {
        $st = DB::getDB()->prepare('INSERT INTO vakt SET vakttype=:vakttype, dato=:dato, autogenerert=0');
        $st->bindParam(':vakttype', $vakttype);
        $st->bindParam(':dato', $dato);
        $st->execute();
    }

    public static function byttVakt($vaktId1, $vaktId2)
    {
        //Hent ut relevant info.
        $st_1 = DB::getDB()->prepare('SELECT bruker_id FROM vakt WHERE id=:id');
        $st_1->bindParam(':id', $vaktId1);
        $st_1->execute();
        $forste = $st_1->fetchColumn();

        $st_2 = DB::getDB()->prepare('SELECT bruker_id FROM vakt WHERE id=:id');
        $st_2->bindParam(':id', $vaktId2);
        $st_2->execute();
        $andre = $st_2->fetchColumn();

        //Oppdatere de to.
        $st_3 = DB::getDB()->prepare('UPDATE vakt SET bruker_id=:bruker_id WHERE id=:id');
        $st_3->bindParam(':bruker_id', $andre);
        $st_3->bindParam(':id', $vaktId1);
        $st_3->execute();

        $st_4 = DB::getDB()->prepare('UPDATE vakt SET bruker_id=:bruker_id WHERE id=:id');
        $st_4->bindParam(':bruker_id', $forste);
        $st_4->bindParam(':id', $vaktId2);
        $st_4->execute();
    }

    public function erDobbelvakt()
    {
        return $this->dobbelvakt == 1;
    }

    public function endreDobbelvakt() {
        if($this->erDobbelvakt()) {
            $this->dobbelvakt = 0;
        } else {
            $this->dobbelvakt = 1;
        }
        self::oppdater();
    }

    public static function setDobbelvakt($vaktId)
    {
        $st = DB::getDB()->prepare('UPDATE vakt SET dobbelvakt=1-dobbelvakt WHERE id=:id');
        $st->bindParam(':id', $vaktId);
        $st->execute();
    }

    public function erStraffevakt()
    {
        return $this->straffevakt == 1;
    }

    public static function setStraffevakt($vaktId)
    {
        $aktuell_vakt = Vakt::medId($vaktId);
        if ($aktuell_vakt != null) {
            $straffevakt = $aktuell_vakt->erStraffevakt() ? 0 : 1;
            $st = DB::getDB()->prepare('UPDATE vakt SET straffevakt=:straffevakt WHERE id=:id');
            $st->bindParam(':id', $vaktId);
            $st->bindParam(':straffevakt', $straffevakt);
            $st->execute();
        }
    }

    public static function slettVakt($vaktId)
    {
        $st = DB::getDB()->prepare('UPDATE vakt SET bruker_id=0 WHERE id=:id');
        $st->bindParam(':id', $vaktId);
        $st->execute();
    }

    public static function getVakterByDato($dato)
    {
        $st = DB::getDB()->prepare('SELECT * FROM vakt WHERE dato=:dato');
        $st->bindParam(':dato', $dato);
        $st->execute();
        $vakter = array();
        for ($i = 0; $i < $st->rowCount(); $i++) {
            $vakter[] = self::init($st);
        }
        return $vakter;
    }

    public static function alleVakterEtterDato($dato)
    {

        $st = DB::getDB()->prepare('SELECT * FROM vakt WHERE dato>:dato');
        $st->bindParam(':dato', $dato);
        $st->execute();
        $vakter = array();

        for ($i = 0; $i < $st->rowCount(); $i++) {
            $vakter[] = self::init($st);
        }
        return $vakter;
    }

    public static function alleVakterEtterDatoMedVaktbytte($dato)
    {
        $st = DB::getDB()->prepare('SELECT * FROM vakt WHERE (dato>:dato AND vaktbytte_id IS NOT NULL AND bytte IS NOT NULL)');
        $st->bindParam(':dato', $dato);
        $st->execute();
        $vakter = array();

        for ($i = 0; $i < $st->rowCount(); $i++) {
            $vakter[] = self::init($st);
        }
        return $vakter;
    }

    public function fjernFraAlleBytter()
    {

        foreach (Vaktbytte::getAlle() as $vaktbytte) {
            /* @var \intern3\Vaktbytte $vaktbytte */
            if (in_array($this->id, $vaktbytte->getForslagIder())) {
                $vaktbytte->slettForslag($this->id);
            }
        }
    }

    public function setBruker($brukerId)
    {

        if (($brukeren = Bruker::medId($brukerId)) != null) {

            $this->brukerId = $brukerId;
            $this->bruker = $brukeren;
            $this->oppdater();
        }
    }


    private function oppdater()
    {
        $st = DB::getDB()->prepare('UPDATE vakt SET bruker_id=:bid, vakttype=:typen, dato=:dato, 
        bytte=:bytte, bekreftet=:bekreftet, autogenerert=:autogenerert, dobbelvakt=:dobbelvakt, straffevakt=:straffevakt, vaktbytte_id=:vaktbytte WHERE id=:id');

        $bytte = $this->bytte ? 1 : 0;
        $vaktbytter = implode(',', $this->vaktbytteDenneErMedI);

        $st->bindParam(':id', $this->id);
        $st->bindParam(':bid', $this->brukerId);
        $st->bindParam(':typen', $this->vakttype);
        $st->bindParam(':dato', $this->dato);
        $st->bindParam(':bytte', $bytte);
        $st->bindParam(':bekreftet', $this->bekreftet);
        $st->bindParam(':autogenerert', $this->autogenerert);
        $st->bindParam(':dobbelvakt', $this->dobbelvakt);
        $st->bindParam(':straffevakt', $this->straffevakt);
        $st->bindParam(':vaktbytte', $vaktbytter);

        $st->execute();

    }

    public static function antallKjipeAutogenererte()
    {

        $st = DB::getDB()->prepare('SELECT count(id) AS sum FROM vakt WHERE ( 
                                          (
                                            (DAYOFWEEK(dato) = 6 AND vakttype IN (3, 4) ) 
                                            OR (DAYOFWEEK(dato) = 7 AND vakttype IN (2,3,4) ) 
                                            OR (DAYOFWEEK(dato) = 1 AND vakttype IN (2))
                                            OR vakttype = 1
                                            )
                                            AND autogenerert=0
                                          )');

        $st->execute();
        return $st->fetch()["sum"];
    }

    public function erKjip()
    {

        if ($this->vakttype == 1) {
            return true;
        }

        $dayNumber = date('N', strtotime($this->dato));

        if (
            $dayNumber == 6
            || ($dayNumber == 7 && in_array($this->vakttype, array('2', '3')))
            || ($dayNumber == 5 && in_array($this->vakttype, array('3', '4')))
        ) {
            return true;
        }

        return false;

    }

    public static function timeCompare(Vakt $a, Vakt $b) : int {
        return abs(strtotime($a->getDato()) - strtotime($b->getDato()));
    }

    public function toggleByttemarked() {
        if ($this->getBytte() && $this->getVaktbytte() != NULL) {
            $st = DB::getDB()->prepare('DELETE FROM vaktbytte WHERE id=:id');
            $st->execute(['id'=>$this->getVaktbytte()->getId()]);

            $st_1 = DB::getDB()->prepare('UPDATE vakt SET vaktbytte_id=0 WHERE id=:id');
            $st_1->execute(['id'=>$this->getId()]);
        } else {
            $st = DB::getDB()->prepare('INSERT INTO vaktbytte (vakt_id,gisbort) VALUES(:vakt_id,1)');
            $st->execute(['vakt_id'=>$this->getId()]);
            $vaktbyttet = $this->getVaktbytte();

            $st_1 = DB::getDB()->prepare('UPDATE vakt SET bytte=1,vaktbytte_id=:vaktbyttet WHERE id=:id');
            $st_1->execute(['id'=>$this->getId(), 'vaktbyttet'=>$vaktbyttet->getId()]);
        }
    }
}