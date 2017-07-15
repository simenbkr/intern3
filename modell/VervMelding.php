<?php
/**
 * Created by PhpStorm.
 * User: Simen
 * Date: 02.02.2017
 * Time: 19:36
 */

namespace intern3;


class VervMelding
{
    private $id;
    private $dato;
    private $tekst;
    private $verv_id;
    private $beboer_id;

    public static function medId($id)
    {
        $st = DB::getDB()->prepare('SELECT * FROM verv_melding WHERE id=:id');
        $st->bindParam(':id', $id);
        $st->execute();
        return self::init($st);
    }

    public static function medVervId($vervId){
        $st = DB::getDB()->prepare('SELECT * FROM verv_melding WHERE verv_id=:verv_id ORDER BY dato DESC');
        $st->bindParam(':verv_id', $vervId);
        $st->execute();

        $verv_meldinger = array();
        for($i = 0; $i < $st->rowCount(); $i++){
            $verv_meldinger[] = self::init($st);
        }
        return $verv_meldinger;
    }

    public static function medBeboerId($beboerId){
        $st = DB::getDB()->prepare('SELECT * FROM verv_melding WHERE beboer_id=:beboer_id ORDER BY dato DESC');
        $st->bindParam(':beboer_id', $beboerId);
        $st->execute();

        $verv_meldinger = array();
        for($i = 0; $i < $st->rowCount(); $i++){
            $verv_meldinger[] = self::init($st);
        }
        return $verv_meldinger;
    }


    private static function init(\PDOStatement $st)
    {
        $rad = $st->fetch();
        if ($rad == null) {
            return null;
        }
        $instance = new self();
        $instance->id = $rad['id'];
        $instance->dato = $rad['dato'];
        $instance->tekst =  $rad['tekst'];
        $instance->verv_id = $rad['verv_id'];
        $instance->beboer_id = $rad['beboer_id'];
        return $instance;
    }

    public function getId(){
        return $this->id;
    }

    public function getBeboer(){
        if(Beboer::medId($this->beboer_id) != null){
            return Beboer::medId($this->beboer_id);
        }
        return null;
    }

    public function getVerv(){
        if(Verv::medId($this->verv_id) != null){
            return Verv::medId($this->verv_id);
        }
        return null;
    }

    public function getDato(){
        return $this->dato;
    }

    public function getTekst(){
        return $this->tekst;
    }

    public function getVervId(){
        return $this->verv_id;
    }

    public function getBeboerId(){
        return $this->beboer_id;
    }

    public static function getTreSiste(){
        $st = DB::getDB()->prepare('SELECT * FROM verv_melding ORDER BY dato DESC LIMIT 3');
        $st->execute();
        $verv_meldingene = array();
        for($i = 0; $i < $st->rowCount(); $i++){
            $melding = self::init($st);
            //Eh, fuck eldre meldinger, right?
            if(strtotime($melding->getDato()) > strtotime('-30 days')){
                $verv_meldingene[] = $melding;
            }
        }
        return $verv_meldingene;
    }
}