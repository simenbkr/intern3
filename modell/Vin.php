<?php

namespace intern3;


class Vin
{
    private $id;
    private $navn;
    private $bilde;
    private $pris;
    private $antall;
    private $typeId;
    private $type;
    private $svinn;
    private $avanse;
    private $slettet;
    private $beskrivelse;

    private static function init(\PDOStatement $st) {
        $rad = $st->fetch();
        if ($rad == null) {
            return null;
        }
        $instance = new self();
        $instance->id = $rad['id'];
        $instance->navn = $rad['navn'];
        $instance->bilde = $rad['bilde'];
        $instance->pris = $rad['pris'];
        $instance->antall = $rad['antall'];
        $instance->typeId = $rad['typeId'];
        $instance->avanse = $rad['avanse'];
        $instance->slettet = $rad['slettet'];
        $instance->beskrivelse = $rad['beskrivelse'];
        $instance->type = Vintype::medId($instance->typeId);
        $instance->svinn = 0;
        $st = DB::getDB()->prepare('SELECT * FROM vinsvinn WHERE vin_id=:id');
        $st->bindParam(':id', $instance->id);
        $st->execute();
        foreach($st->fetchAll() as $rad){
            $instance->svinn += $rad['antall'];
        }
        return $instance;
    }

    public static function medId($id){
        $st = DB::getDB()->prepare('SELECT * FROM vin WHERE id=:id');
        $st->bindParam(':id', $id);
        $st->execute();
        return self::init($st);
    }

    public static function medNavn($navn){
        $st = DB::getDB()->prepare('SELECT * from vin WHERE navn=:navn');
        $st->bindParam(':navn', $navn);
        $st->execute();
        return self::init($st);
    }

    public function getId(){
        return $this->id;
    }

    public function getNavn(){
        return $this->navn;
    }

    public function getBilde(){
        return $this->bilde;
    }

    public function getPris(){
        return $this->pris;
    }

    public function getAntall(){
        return $this->antall;
    }

    public function getTypeId(){
        return $this->typeId;
    }

    public function getType(){
        return $this->type;
    }

    public function getSvinn(){
        return $this->svinn;
    }

    public function getBeskrivelse(){
        return $this->beskrivelse;
    }

    public function erSlettet(){
        return $this->slettet == 1;
    }

    public static function getAlle(){
        $st = DB::getDB()->prepare('SELECT * FROM vin ORDER BY navn ASC');
        $st->execute();
        $vinene = array();
        for($i = 0; $i < $st->rowCount(); $i++){
            $vinene[] = self::init($st);
        }
        return $vinene;
    }

    public function getAvanse(){
        return $this->avanse;
    }

    public static function getAktive(){
        $st = DB::getDB()->prepare('SELECT * FROM vin WHERE slettet=0');
        $st->execute();
        $vinene = array();
        for($i = 0; $i < $st->rowCount(); $i++){
            $vinene[] = self::init($st);
        }
        return $vinene;
    }

    public static function getAktiveAvType($type){
        $typeId = $type->getId();

        $st = DB::getDB()->prepare('SELECT * FROM vin WHERE (slettet=0 AND typeId=:typeId)');
        $st->bindParam(':typeId', $typeId);
        $st->execute();
        $vinene = array();
        for($i = 0; $i < $st->rowCount(); $i++){
            $vinene[] = self::init($st);
        }
        return $vinene;

    }

}
?>