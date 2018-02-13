<?php

namespace intern3;


class Prefs
{

    private $beboerId;
    private $resepp;
    private $vinkjeller;
    private $pinboo;
    private $pinkode;
    private $vinpinboo;
    private $vinpin;

    private static function init(\PDOStatement $st){
        $instance = new self();

        $rad = $st->fetch();
        if ($rad == null) {
            return null;
        }

        $instance->beboerId = $rad['beboerId'];
        $instance->resepp = $rad['resepp'];
        $instance->vinkjeller = $rad['vinkjeller'];
        $instance->pinboo = $rad['pinboo'];
        $instance->pinkode = $rad['pinkode'];
        $instance->vinpinboo = $rad['vinpinboo'];
        $instance->vinpin = $rad['vinpin'];

        return $instance;
    }

    public static function fraBeboerId($beboerId){
        $st = DB::getDB()->prepare('SELECT * FROM prefs WHERE beboerId=:id LIMIT 1');
        $st->bindParam(':id', $beboerId);
        $st->execute();

        return self::init($st);
    }

    public function getBeboerId(){
        return $this->beboerId;
    }

    public function getResepp(){
        return $this->resepp == 1;
    }

    public function getVinkjeller(){
        return $this->vinkjeller == 1;
    }

    public function harPinkode(){
        return $this->pinboo == 1;
    }

    public function getPinkode(){
        return $this->pinkode;
    }

    public function harVinPin(){
        //Må ha vinpin.
        return true;
    }

    public function getVinPinkode(){
        return ($this->vinpin == null ? null : $this->vinpin);
    }




}