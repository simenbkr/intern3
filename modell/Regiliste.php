<?php

namespace intern3;


class Regiliste
{

    private $id;
    private $navn;
    private $beboerliste;

    private static function init(\PDOStatement $st){

        $rad = $st->fetch();
        if($rad === null){
            return null;
        }

        $instans = new self();
        $instans->id = $rad['id'];
        $instans->navn = $rad['navn'];

        $instans->beboerliste = array();

        $st = DB::getDB()->prepare('SELECT beboer_id AS bebid FROM regiliste_beboer WHERE regiliste_id = :id');
        //TODO Endre slik at den kun velger aktive beboere.

        $st->bindParam(':id', $instans->id);
        $st->execute();

        for($i = 0; $i < $st->rowCount(); $i++){
            $rad = $st->fetch();
            $instans->beboerliste[] = Beboer::medId($rad['bebid']);
        }

        return $instans;
    }

    public static function getAlleLister(){

        $st = DB::getDB()->prepare('SELECT * FROM regiliste');
        $st->execute();
        $lister = array();

        for($i = 0; $i < $st->rowCount(); $i++){
            $lister[] = self::init($st);
        }

        return $lister;
    }

    public static function medId($id){
        $st = DB::getDB()->prepare('SELECT * FROM regiliste WHERE id=:id');
        $st->bindParam(':id', $id);
        return self::init($st);
    }


    public function getId(){
        return $this->id;
    }

    public function getNavn(){
        return $this->navn;
    }

    public function getBeboerliste(){
        return $this->beboerliste;
    }



}