<?php

namespace intern3;


class Vintype
{
    private $id;
    private $navn;

    private static function init(\PDOStatement $st)
    {
        $rad = $st->fetch();
        if ($rad == null) {
            return null;
        }
        $instance = new self;
        $instance->id = $rad['id'];
        $instance->navn = $rad['navn'];
        return $instance;
    }

    public static function medId($id){
        $st = DB::getDB()->prepare('SELECT * FROM vintype WHERE id=:id');
        $st->bindParam(':id', $id);
        $st->execute();
        return self::init($st);
    }

    public function getId(){
        return $this->id;
    }

    public function getNavn(){
        return $this->navn;
    }

    public static function getAlle(){
        $vintypene = array();
        $st = DB::getDB()->prepare('SELECT * FROM vintype');
        $st->execute();
        for($i = 0; $i < $st->rowCount(); $i++){
            $vintypene[] = self::init($st);
        }
        return $vintypene;
    }

    public static function antallVinAvType($typeId){
        $st = DB::getDB()->prepare('SELECT * FROM vin WHERE typeId=:id');
        $st->bindParam(':id', $typeId);
        $st->execute();
        return $st->rowCount();
    }

}