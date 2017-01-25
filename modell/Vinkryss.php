<?php


namespace intern3;


class Vinkryss
{
    private $id;
    private $antall;
    private $tiden;
    private $fakturert;
    private $vinId;
    private $vinen;
    private $beboerId;
    private $beboeren;

    private static function init(\PDOStatement $st) {
        $rad = $st->fetch();
        if ($rad == null) {
            return null;
        }
        $instance = new self();
        $instance->id = $rad['id'];
        $instance->antall = $rad['antall'];
        $instance->tiden = $rad['tiden'];
        $instance->fakturert = $rad['fakturert'];
        $instance->vinId = $rad['vinId'];
        $instance->vinen = Vin::medId($instance->vinId);
        $instance->beboerId = $rad['beboerId'];
        $instance->beboeren = Beboer::medId($instance->beboerId);
        return $instance;
    }

    public static function medId($id){
        $st = DB::getDB()->prepare('SELECT * FROM vinkryss WHERE id=:id');
        $st->bindParam(':id', $id);
        $st->execute();
        return self::init($st);
    }

    public static function medBeboerId($id){
        $st = DB::getDB()->prepare('SELECT * FROM vinkryss WHERE beboerId=:id');
        $st->bindParam(':id', $id);
        $st->execute();
        $vinkryssListe = array();
        for($i = 0; $i < $st->rowCount(); $i++){
            $vinkryssListe[] = self::init($st);
        }
        return $vinkryssListe; //vinkryss-objekter.
    }

    public function getId(){
        return $this->id;
    }

    public function getAntall(){
        return $this->antall;
    }

    public function getTiden(){
        return $this->tiden;
    }

    public function getFakturert(){
        return $this->fakturert;
    }

    public function getVinId(){
        return $this->vinId;
    }

    public function getVin(){
        return $this->vinen;
    }

    public function getBeboerId(){
        return $this->beboerId;
    }

    public function getBeboer(){
        return $this->beboeren;
    }

    public function getKostnad(){
        return $this->antall * $this->vinen->getPris();
    }

    public static function getAlleIkkeFakturert(){
        $st = DB::getDB()->prepare('SELECT * FROM vinkryss WHERE fakturert=0');
        $st->execute();
        $objektene = array();
        for($i = 0; $i < $st->rowCount(); $i++){
            $objektene[] = self::init($st);
        }
        return $objektene;
    }

    public static function getAlleIkkeFakturertByBeboerId($beboerId){
        $st = DB::getDB()->prepare('SELECT * FROM vinkryss WHERE (fakturert=0 AND beboerId=:id)');
        $st->bindParam(':id', $beboerId);
        $st->execute();
        $objektene = array();
        for($i = 0; $i < $st->rowCount(); $i++){
            $objektene[] = self::init($st);
        }
        return $objektene;
    }

    public static function getAlleFakturerte(){
        $st = DB::getDB()->prepare('SELECT * FROM vinkryss WHERE fakturert=1');
        $st->execute();
        $objektene = array();
        for($i = 0; $i < $st->rowCount(); $i++){
            $objektene[] = self::init($st);
        }
        return $objektene;
    }

    public static function getAlleFakturertByBeboerId($beboerId){
        $st = DB::getDB()->prepare('SELECT * FROM vinkryss WHERE (fakturert=1 AND beboerId=:id)');
        $st->bindParam(':id', $beboerId);
        $st->execute();
        $objektene = array();
        for($i = 0; $i < $st->rowCount(); $i++){
            $objektene[] = self::init($st);
        }
        return $objektene;
    }


}