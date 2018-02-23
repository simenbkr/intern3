<?php

namespace intern3;

class Vinregel
{
    
    private $id;
    private $tekst;
    
    private static function init(\PDOStatement $st)
    {
        $rad = $st->fetch();
        if ($rad == null) {
            return null;
        }
        $instance = new self();
        
        $instance->id = $rad['id'];
        $instance->tekst = $rad['tekst'];
        
        return $instance;
    }
    
    private function oppdater()
    {
        $st = DB::getDB()->prepare('UPDATE vin_regler SET tekst=:tekst WHERE id=:id');
        $st->bindParam(':id', $this->id);
        $st->bindParam(':tekst', $this->id);
        $st->execute();
    }
    
    public static function medId($id)
    {
        $st = DB::getDB()->prepare('SELECT * FROM vin_regler WHERE id=:id');
        $st->bindParam(':id', $id);
        $st->execute();
        return self::init($st);
    }
    
    public function getId(): int
    {
        return $this->id;
    }
    
    public function getTekst(): string
    {
        return $this->tekst;
    }
    
    public function setTekst($tekst)
    {
        $this->tekst = $tekst;
        $this->oppdater();
    }
    
    public static function getRegel() : Vinregel
    {
        $st = DB::getDB()->prepare('SELECT * FROM vin_regler ORDER BY id DESC LIMIT 1');
        $st->execute();
        return self::init($st);
    }
    
    public static function nyRegel($tekst) : Vinregel {
        
        $st = DB::getDB()->prepare('INSERT INTO vin_regler (tekst) VALUES(:tekst)');
        $st->bindParam(':tekst', $tekst);
        $st->execute();
        
        return self::getRegel();
    }
    
}
