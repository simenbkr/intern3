<?php

class HelgaGjest {

    private $navn;
    private $epost;
    private $vert;

    public function __construct($epost,$navn,$vert)
    {
        $this->epost = $epost;
        $this->navn = $navn;
        $this->vert = $vert;
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
}

?>