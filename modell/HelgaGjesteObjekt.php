<?php


namespace intern3;


class HelgaGjesteObjekt
{
    private $navn;
    private $epost;
    private $ider;
    private $aar;
    private $dager;
    private $billetter;
    private $vert;
    private $vertId;
    private $inne;
    private $sendt;
    private $helgaGjestinstanser;

    public static function init(\PDOStatement $st)
    {

        $instance = new self();
        $instance->ider = array();
        $instance->dager = array();
        $instance->billetter = array();
        $instance->inne = array();
        $instance->sendt = array();
        $instance->helgaGjestinstanser = array();

        while ($rad = $st->fetch()) {
            $instance->navn = $rad['navn'];
            $instance->epost = $rad['epost'];
            $instance->ider[] = $rad['id'];
            $instance->aar = $rad['aar'];
            $instance->dager[] = $rad['dag'];
            $instance->billetter[] = $rad['api_nokkel'];
            $instance->vertId = $rad['vert'];
            $instance->sendt[] = $rad['sendt_epost'];
        }

        foreach ($instance->ider as $id) {
            $instance->helgaGjestinstanser[] = HelgaGjest::medId($id);
        }

        return $instance;
    }

    public static function medAarVert($aar, $vertId)
    {
        $st = DB::getDB()->prepare('SELECT * FROM helgagjest WHERE (aar = :aar AND vert = :vert) GROUP BY epost');
        $st->execute(['aar' => $aar, 'vert' => $vertId]);
        $gjester = array();

        while ($rad = $st->fetch()) {
            $gjester[] = self::medAarEpost($aar, $rad['epost']);
        }

        return $gjester;
    }

    public static function medAarEpost($aar, $epost)
    {
        $st = DB::getDB()->prepare('SELECT * FROM helgagjest WHERE (aar = :aar AND epost = :epost)');
        $st->execute(['aar' => $aar, 'epost' => $epost]);
        return self::init($st);
    }


    public function getVert()
    {
        if (is_null($this->vert)) {
            $this->vert = Beboer::medId($this->vertId);
        }
        return $this->vert;
    }

    public function getInstanser()
    {
        return $this->helgaGjestinstanser;
    }

    public function getIder()
    {
        return $this->ider;
    }

    public function getDager()
    {
        return $this->dager;
    }

    public function getBilletter()
    {
        return $this->billetter;
    }

    public function getInne()
    {
        return $this->inne;
    }

    public function getNavn()
    {
        return $this->navn;
    }

    public function getEpost()
    {
        return $this->epost;
    }

    public function setEpost($epost) {
        $st = DB::getDB()->prepare('UPDATE helgagjest SET epost=:ny WHERE (epost = :epost AND aar = :aar)');
        $st->execute(['epost' => $this->epost, 'ny' => $epost, 'aar' => $this->aar]);
    }

    public function getAar()
    {
        return $this->aar;
    }

    public function slett()
    {
        foreach ($this->ider as $id) {
            HelgaGjest::removeGjest($id);
        }
    }

    public function erEpostSendt()
    {
        foreach ($this->sendt as $s) {
            if ($s == 0) {
                return false;
            }
        }
        return true;
    }

    public static function addMultidayGjest($navn, $epost, $aar, $dager, Beboer $vert)
    {
        foreach ($dager as $dag) {
            HelgaGjest::addGjest($navn, $epost, $vert->getId(), $dag, $aar);
        }
    }

    public function gjestTorsdag() {
        foreach($this->dager as $dag) {
            if($dag === '0') {
                return true;
            }
        }
        return false;
    }

    public function gjestFredag() {
        foreach($this->dager as $dag) {
            if($dag === '1') {
                return true;
            }
        }
        return false;
    }

    public function gjestLordag() {
        foreach($this->dager as $dag) {
            if($dag === '2') {
                return true;
            }
        }
        return false;
    }

}