<?php

namespace intern3;

class HelgaGjest {

    private $id;
    private $navn;
    private $epost;
    private $vertId;
    private $vert;
    private $sendt_epost;
    private $inne;
    private $dag;
    private $aar;
    private $api_nokkel;

    public static function init(\PDOStatement $st)
    {
        $rad = $st->fetch();
        if ($rad == null) {
            return null;
        }
        $instance = new self();
        $instance->id = $rad['id'];
        $instance->navn = $rad['navn'];
        $instance->epost = $rad['epost'];
        $instance->vertId = $rad['vert'];
        $instance->sendt_epost = $rad['sendt_epost'];
        $instance->inne = $rad['inne'];
        $instance->dag = $rad['dag'];
        $instance->aar = $rad['aar'];
        $instance->api_nokkel = $rad['api_nokkel'];
        return $instance;
    }

    public static function byRow($rad){
        //Legacyshit allerede mann.
        if($rad == null){
            return null;
        }
        $instance = new self();
        $instance->id = $rad['id'];
        $instance->navn = $rad['navn'];
        $instance->epost = $rad['epost'];
        $instance->vertId = $rad['vert'];
        $instance->sendt_epost = $rad['sendt_epost'];
        $instance->inne = $rad['inne'];
        $instance->dag = $rad['dag'];
        $instance->aar = $rad['aar'];
        $instance->api_nokkel = $rad['api_nokkel'];
        return $instance;
    }


    public function getId(){
        return $this->id;
    }

    public function getEpost(){
        return $this->epost;
    }

    public function getNokkel(){
        return $this->api_nokkel;
    }

    public function getNavn(){
        return $this->navn;
    }

    public function getVertId(){
        return $this->vertId;
    }

    public function getVert(){
        return Beboer::medId($this->vertId);
    }

    public function getSendt(){
        return $this->sendt_epost;
    }

    public function getInne(){
        return $this->inne;
    }

    public function getDag(){
        return $this->dag;
    }

    public function getAar(){
        return $this->aar;
    }

    public function setDag($dag){
        $this->dag = $dag;
        $this->oppdater();
    }

    public function setEpost($epost){
        $this->epost = $epost;
        $this->oppdater();
    }

    public function setNavn($navn){
        $this->navn = $navn;
        $this->oppdater();
    }

    public function setVert($beboer_id){
        $this->vert = $beboer_id;
        $this->oppdater();
    }

    public function setSendt($sendt){
        $this->sendt_epost = $sendt;
        $this->oppdater();
    }

    public function setInne($inne){
        $this->inne = $inne;
        $this->oppdater();
    }


    private function oppdater(){
        $st = DB::getDB()->prepare('UPDATE helgagjest SET epost=:epost, navn=:navn, vert=:vert, inne=:inne,sendt_epost=:sendt_epost WHERE id=:id');
        $st->bindParam(':epost', $this->epost);
        $st->bindParam(':navn', $this->navn);
        $st->bindParam(':vert', $this->vertId);
        $st->bindParam(':inne', $this->inne);
        $st->bindParam(':sendt_epost', $this->sendt_epost);
        $st->bindParam(':id', $this->id);
        $st->execute();
    }

    public static function byId($id){
        $st = DB::getDB()->prepare('SELECT * FROM helgagjest WHERE id=:id');
        $st->bindParam(':id', $id);
        $st->execute();
        return self::init($st);
    }

    public static function addGjest($navn, $epost, $vertId, $dag, $aar, $inne=0, $sendt_epost=0){
        $st = DB::getDB()->prepare('INSERT INTO helgagjest (navn, aar, epost, vert, dag ,inne, sendt_epost) VALUES(:navn, :aar, :epost, :vert, :dag, :inne, :sendt_epost)');
        $st->bindParam(':navn',     $navn);
        $st->bindParam(':aar',      $aar);
        $st->bindParam(':epost',    $epost);
        $st->bindParam(':vert',     $vertId);
        $st->bindParam(':inne',     $inne);
        $st->bindParam(':sendt_epost',  $sendt_epost);
        $st->bindParam(':dag',      $dag);
        $st->execute();
    }

    public static function removeGjest($gjestid){
        $st = DB::getDB()->prepare('DELETE FROM helgagjest WHERE id=:id');
        $st->bindParam(':id', $gjestid);
        $st->execute();
    }

    public static function belongsToBeboer($gjesteid, $beboerid){
        $st = DB::getDB()->prepare('SELECT * FROM helgagjest WHERE (id=:id AND vert=:vert)');
        $st->bindParam(':id', $gjesteid);
        $st->bindParam(':vert', $beboerid);
        $st->execute();
        return $st->rowCount() > 0;
    }
}
?>