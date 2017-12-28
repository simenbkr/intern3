<?php

namespace intern3;

class Helgaverv {

    private $id;
    private $navn;
    private $tilgang;

    private static function init(\PDOStatement $st){
        $rad = $st->fetch();
        if($rad == null){
            return null;
        }

        $instance = new self();
        $instance->id = $rad['id'];
        $instance->navn = $rad['navn'];
        $instance->tilgang = $rad['tilgang'];
        return $instance;
    }

    public static function medId($id){
        $st = DB::getDB()->prepare('SELECT * FROM helgaverv WHERE id=:id');
        $st->bindParam(':id', $id);
        $st->execute();
        return self::init($st);
    }

    public static function medNavn($navn){
        $st = DB::getDB()->prepare('SELECT * FROM helgaverv WHERE navn=:navn');
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

    public function getTilgang(){
        return $this->tilgang;
    }

    public function setNavn($navn){
        $this->navn = $navn;
        $this->oppdater();
    }

    public function setTilgang($tilgang){
        $this->tilgang = $tilgang;
        $this->oppdater();
    }

    public function harInngangTilgang(){
        return $this->tilgang > 0;
    }

    private function oppdater(){
        $st = DB::getDB()->prepare('UPDATE helgaverv SET navn=:navn, tilgang=:tilgang WHERE id=:id');
        $st->bindParam(':navn', $this->navn);
        $st->bindParam(':tilgang', $this->tilgang);
        $st->bindParam(':id', $this->id);
        $st->execute();
    }

    public function getAnsvarlige(){
        $st = DB::getDB()->prepare('SELECT * FROM beboer WHERE beboer.id IN (
              SELECT hvb.beboer_id FROM helgaverv_beboer AS hvb WHERE id=:id)');

        $st->bindParam(':id', $this->id);
        $st->execute();

        $beboere = array();
        for($i = 0; $i < $st->rowCount(); $i++){
            $beboere[] = Beboer::medId($st->fetch()['id']);
        }
        return $beboere;
    }

    public function fjern($beboerId){
        $st = DB::getDB()->prepare('DELETE FROM helgaverv_beboer WHERE (id=:id AND beboer_id=:beboer_id)');
        $st->bindParam(':id', $this->id);
        $st->bindParam(':beboer_id', $beboerId);
        $st->execute();
    }

    public function leggTil($beboerId){
        $st = DB::getDB()->prepare('INSERT INTO helgaverv_beboer (id,beboer_id) VALUES(:id,:beboer_id)');
        $st->bindParam(':id', $this->id);
        $st->bindParam(':beboer_id', $beboerId);
        $st->execute();
    }

    public static function getAlle(){
        $st = DB::getDB()->prepare('SELECT * FROM helgaverv');
        $st->execute();
        $lista = array();
        for($i = 0; $i < $st->rowCount(); $i++){
            $lista[] = self::init($st);
        }
        return $lista;
    }

}

?>