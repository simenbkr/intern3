<?php

namespace intern3;


class ArbeidBilde
{
    private $id;
    private $filnavn;
    private $arbeid_id;
    private $path;
    
    
    private static function init(\PDOStatement $st){
        
        $rad = $st->fetch();
        if ($rad == null) {
            return null;
        }
    
        $instance = new self();
        $instance->id = $rad['id'];
        $instance->filnavn = $rad['filnavn'];
        $instance->arbeid_id = $rad['arbeid_id'];
        $instance->path = REGIBILDER . $instance->filnavn;
        return $instance;
    }
    
    public static function medId($id){
        $st = DB::getDB()->prepare('SELECT * FROM arbeid_bilder WHERE id=:id');
        $st->bindParam(':id', $id);
        $st->execute();
        return self::init($st);
    }
    
    public function getId(){
        return $this->id;
    }
    
    public function getPath(){
        return $this->path;
    }
    
    public function getFilnavn(){
        return $this->filnavn;
    }
    
    public function getArbeidId(){
        return $this->arbeid_id;
    }
    
    public static function medArbeidId($arbeidId){
        $st = DB::getDB()->prepare('SELECT * FROM arbeid_bilder WHERE arbeid_id=:arbeidId');
        $st->bindParam(':arbeidId', $arbeidId);
        $st->execute();
        
        $bilder = array();
        
        for($i = 0; $i < $st->rowCount(); $i++){
            $bilder[] = self::init($st);
        }
        
        return $bilder;
    }
    
    public static function opprett($filnavn, $arbeid_id){
        $st = DB::getDB()->prepare('INSERT INTO arbeid_bilder (filnavn, arbeid_id) VALUES(:filnavn, :arbeid_id)');
        $st->bindParam(':filnavn', $filnavn);
        $st->bindParam(':arbeid_id', $arbeid_id);
        
        $st->execute();
    }
    
    public function slett(){
    
        unlink(dirname(__DIR__) . '/www/regibilder/' . $this->filnavn);
        $st = DB::getDB()->prepare('DELETE FROM arbeid_bilder WHERE id=:id');
        $st->bindParam(':id', $this->id);
        $st->execute();
    }
    
}