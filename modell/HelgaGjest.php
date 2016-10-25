<?php

namespace intern3;

class HelgaGjest {

    private $id;
    private $navn;
    private $epost;
    private $vert;
    private $sendt_epost;
    private $inne;

    public function __construct($id, $epost,$navn,$vert, $sendt_epost = null, $inne = null)
    {
        $this->id = $id;
        $this->epost = $epost;
        $this->navn = $navn;
        $this->vert = $vert;
        $this->inne = $inne;
        $this->sendt_epost = $sendt_epost;
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
        $st = DB::getDB()->prepare('UPDATE helgagjest SET epost=:epost, navn=:navn, vert=:vert, inne=:inne,sendt_epost=:sendt_epost');
        $st->bindParam(':epost', $this->epost);
        $st->bindParam(':navn', $this->navn);
        $st->bindParam(':vert', $this->vert);
        $st->bindParam(':inne', $this->inne);
        $st->bindParam(':sendt_epost', $this->sendt_epost);
    }

}
?>