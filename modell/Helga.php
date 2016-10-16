<?php


class Helga {

    private $dato;
    private $generaler;
    private $tema;
    public function __construct($dato,$generaler,$tema=null)
    {
        $this->dato = $dato;
        $this->generaler = $generaler;
        $this->tema = $tema;
    }

    public function getDato(){
        return $this->dato;
    }

    public function getGeneraler(){
        return $this->generaler;
    }

}