<?php

namespace intern3;

class Utleie
{

    private $id;
    private $dato;
    private $navn;
    private $beboer1;
    private $beboer2;
    private $beboer3;
    private $rom;

    public static function medId($id)
    {
        $st = DB::getDB()->prepare('SELECT * FROM utleie WHERE id=:id;');
        $st->bindParam(':id', $id);
        $st->execute();
        return self::init($st);
    }

    private static function init(\PDOStatement $st)
    {
        $row = $st->fetch();
        if ($row == null) {
            return null;
        }
        $instance = new self();
        $instance->id = $row['id'];
        $instance->dato = $row['dato'];
        $instance->navn = $row['navn'];
        $instance->beboer1 = $row['beboer1_id'] != null ? Beboer::medId($row['beboer1_id']) : null;
        $instance->beboer2 = $row['beboer2_id'] != null ? Beboer::medId($row['beboer2_id']) : null;
        $instance->beboer3 = $row['beboer3_id'] != null ? Beboer::medId($row['beboer3_id']) : null;
        $instance->rom = $row['rom'];
        return $instance;
    }

    public static function getUtleierFremover()
    {
        $now = date('Y-m-d', time());
        $st = DB::getDB()->prepare('SELECT * FROM utleie WHERE dato>:dato');
        $st->bindParam(':dato', $now);
        $st->execute();

        $lengde = $st->rowCount();
        $utleier = array();
        for ($i = 0; $i < $lengde; $i++) {
            $utleier[] = self::init($st);
        }
        return $utleier;
    }

    private function oppdater()
    {
        $st = DB::getDB()->prepare('UPDATE utleie SET dato=:dato, navn=:navn, beboer1_id=:beboer1_id, beboer2_id=:beboer2_id, beboer3_id=:beboer3_id, rom=:rom WHERE id=:id');
        $st->bindParam(':dato', $this->getDato());
        $st->bindParam(':navn', $this->getNavn());
        $beboer1_id = $this->getBeboer1() != null ? $this->getBeboer1()->getId() : 0;
        $beboer2_id = $this->getBeboer2() != null ? $this->getBeboer2()->getId() : 0;
        $beboer3_id = $this->getBeboer3() != null ? $this->getBeboer3()->getId() : 0;
        $st->bindParam(':beboer1_id', $beboer1_id);
        $st->bindParam(':beboer2_id', $beboer2_id);
        $st->bindParam(':beboer3_id', $beboer3_id);
        $st->bindParam(':rom', $this->getRom());
        $st->bindParam(':id', $this->getId());
        $st->execute();
    }



    public function barvakt1Ledig()
    {
        return $this->getBeboer1() == null;
    }

    public function barvakt2Ledig()
    {
        return $this->getBeboer2() == null;
    }

    public function vaskevaktLedig(){
        return $this->getBeboer3() == null;
    }

    public function erBeboerPameldt($beboer){
        return $beboer != null && in_array($beboer, [$this->beboer1, $this->beboer2, $this->beboer3]);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getDato()
    {
        return $this->dato;
    }

    public function getNavn()
    {
        return $this->navn;
    }

    public function getBeboer1()
    {
        return $this->beboer1;
    }

    public function getBeboer2()
    {
        return $this->beboer2;
    }

    public function getBeboer3()
    {
        return $this->beboer3;
    }

    public function getBarvakter(){
        return array($this->beboer1, $this->beboer2);
    }

    public function getRom()
    {
        return $this->rom;
    }

    public function setDato($dato)
    {
        $this->dato = $dato;
        $this->oppdater();
    }

    public function setNavn($navn)
    {
        $this->navn = $navn;
        $this->oppdater();
    }

    public function setBeboer1($beboer)
    {
        $this->beboer1 = $beboer;
        $this->oppdater();
    }

    public function setBeboer2($beboer)
    {
        $this->beboer2 = $beboer;
        $this->oppdater();
    }

    public function setBeboer3($beboer)
    {
        $this->beboer3 = $beboer;
        $this->oppdater();
    }

    public function setRom($rom)
    {
        $this->rom = $rom;
        $this->oppdater();
    }


}

?>
