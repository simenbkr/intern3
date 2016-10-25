<?php

namespace intern3;

class Helga
{
    private $start_dato;
    private $slutt_dato;
    private $generaler;
    private $tema;
    private $aar;
    private $klar;

    public function __construct($start_dato, $slutt_dato = null, $generaler = null, $tema = null, $klar = null)
    {
        $this->start_dato = date('Y-m-d', strtotime($start_dato));
        $this->slutt_dato = date('Y-m-d', strtotime($start_dato . ' + 2 days'));
        $this->generaler = $generaler;
        $this->tema = $tema;
        $this->aar = date('Y', strtotime($start_dato));
        $this->klar = $klar;
    }

    public function getStartDato()
    {
        return $this->start_dato;
    }

    public function getSluttDato()
    {
        return $this->slutt_dato;
    }

    public function getGeneraler()
    {
        return $this->generaler;
    }

    public function getAar(){
        return $this->aar;
    }

    public function getTema(){
        return $this->tema;
    }

    public function getKlar(){
        return $this->klar != 0;
    }

    public function addGeneral($beboer_id)
    {
        $this->generaler[] = Beboer::medId($beboer_id);
        $this->oppdater();
    }

    public function removeGeneral($beboer_id)
    {
        $nye_generaler = array();
        foreach ($this->generaler as $generalen) {
            if ($generalen->getId() != $beboer_id) {
                $nye_generaler[] = $generalen;
            }
        }
        $this->generaler = $nye_generaler;
        $this->oppdater();
    }

    public function changeTema($nytt_tema)
    {
        $this->tema = $nytt_tema;
        $this->oppdater();
    }

    public function changeDato($ny_start_dato)
    {
        $this->start_dato = date('Y-m-d', strtotime($ny_start_dato));
        $this->slutt_dato = date('Y-m-d', strtotime($ny_start_dato . ' + 2 days'));
        $this->oppdater();
    }

    private function oppdater()
    {
        $general_ider = array();

        foreach($this->generaler as $general){
            $general_ider[] = $general->getId();
        }

        $st = DB::getDB()->prepare('UPDATE helga SET start_dato=:start_dato, slutt_dato=:slutt_dato, generaler=:generaler,tema=:tema WHERE aar=:aar');
        $st->bindParam(':start_dato', $this->start_dato);
        $st->bindParam(':slutt_dato', $this->slutt_dato);
        $st->bindParam(':generaler', json_encode($general_ider));
        $st->bindParam(':tema', $this->tema);
        $st->bindParam(':aar', $this->aar);
        $st->execute();
    }

    public static function getAlleHelga(){

        $helgaene = array();

        $st = DB::getDB()->prepare('SELECT * FROM helga ORDER BY aar DESC');
        $st->execute();
        $rader = $st->fetchAll();

        foreach($rader as $rad){
            $helgaene[] = self::fraSQLRad($rad);
        }
        return $helgaene;
    }

    public static function fraSQLRad($rad){
        return new self($rad['start_dato'],$rad['slutt_dato'], $rad['generaler'], $rad['tema']);
    }

    public static function getLatestHelga()
    {
        $st = DB::getDB()->prepare('SELECT * FROM helga ORDER BY start_dato DESC LIMIT 1');
        $st->execute();
        $rader = $st->fetchAll()[0];

        $generaler = array();
        $json_generaler = json_decode($rader['generaler'], true);
        if ( $json_generaler != null ){
            foreach ($json_generaler as $general) {
                $generaler[] = Beboer::medId($general);
            }
        }

        return new self($rader['start_dato'], $rader['slutt_dato'], $generaler, $rader['tema'], $rader['klar']);
    }

    public static function getHelgaByAar($aar)
    {
        $st = DB::getDB()->prepare('SELECT * from helga WHERE aar=:aar');
        $st->bindParam(':aar', $aar);
        $st->execute();

        $res = $st->fetchAll()[0];

        $generaler = array();
        $json_generaler = json_decode($res['generaler'],true);
        if ($json_generaler != null) {
            foreach ($json_generaler as $general) {
                $generaler_ider[] = $general;
                $generaler[] = Beboer::medId($general);
            }
        }
        return new self($res['start_dato'], $res['slutt_dato'], $generaler, $res['tema'], $res['klar']);
    }

    public static function createHelga($start_dato)
    {
        $aar = date('Y', strtotime($start_dato));
        $slutt_dato = date('Y-m-d', strtotime($start_dato . ' + 2 days'));
        $st = DB::getDB()->prepare('INSERT INTO helga (aar, start_dato, slutt_dato) VALUES(:aar, :start_dato, :slutt_dato)');
        $st->bindParam(':aar', $aar);
        $st->bindParam(':start_dato', $start_dato);
        $st->bindParam(':slutt_dato', $slutt_dato);
        $st->execute();
        return self::getHelgaByAar($aar);
    }

    public function erHelgaGeneral($beboer_id){
        foreach($this->generaler as $general){
            if($general->getId() == $beboer_id) {
                return true;
            }
        }
        return false;
    }

}