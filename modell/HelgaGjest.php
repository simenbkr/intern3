<?php

namespace intern3;

class HelgaGjest {

    private $id;
    private $navn;
    private $epost;
    private $vert;
    private $sendt_epost;
    private $inne;
    private $dag;

    public function __construct($id, $epost,$navn,$vert, $dag, $sendt_epost = null, $inne = null)
    {
        $this->id = $id;
        $this->epost = $epost;
        $this->navn = $navn;
        $this->vert = $vert;
        $this->inne = $inne;
        $this->sendt_epost = $sendt_epost;
        $this->dag = $dag;
    }

    public function getId(){
        return $this->id;
    }

    public function getEpost(){
        return $this->epost;
    }

    public function getNavn(){
        return $this->navn;
    }

    public function getVert(){
        return $this->vert;
    }

    public function getSendt(){
        return $this->sendt_epost;
    }

    public function getInne(){
        return $this->inne;
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
        $st->bindParam(':vert', $this->vert);
        $st->bindParam(':inne', $this->inne);
        $st->bindParam(':sendt_epost', $this->sendt_epost);
        $st->bindParam(':id', $this->id);
        $st->execute();
    }

    public static function byId($id){
        $st = DB::getDB()->prepare('SELECT * FROM helgagjest WHERE id=:id');
        $st->bindParam(':id', $id);
        $st->execute();
        $raden = $st->fetchAll()[0];
        return new self($id, $raden['epost'], $raden['navn'], $raden['vert'], $raden['dag'], $raden['sendt_epost'], $raden['inne']);
    }

    public static function addGjest($navn, $epost, $vert, $dag, $aar, $inne=0, $sendt_epost=0){
        $st = DB::getDB()->prepare('INSERT INTO helgagjest (navn, aar, epost, vert, dag ,inne, sendt_epost) VALUES(:navn, :aar, :epost, :vert, :dag, :inne, :sendt_epost)');
        $st->bindParam(':navn',     $navn);
        $st->bindParam(':aar',      $aar);
        $st->bindParam(':epost',    $epost);
        $st->bindParam(':vert',     $vert);
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