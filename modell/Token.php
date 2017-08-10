<?php

namespace intern3;


class Token {

    private $token;
    private $type;
    private $alphabet = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!#&/_-?";
    private $duration;
    private $time_issued;

    private static function init(\PDOStatement $st){

        $rad = $st->fetch();
        if($rad == null){
            return null;
        }

        $instance = new self();

        $instance->token = $rad['token'];
        $instance->type = $rad['type'];
        $instance->duration = $rad['duration'];
        $instance->time_issued = $rad['time_issued'];

        return $instance;
    }

    public static function byToken($token){
        $st = DB::getDB()->prepare('SELECT * FROM token WHERE token=:token');
        $st->bindParam(':token', $token);
        $st->execute();

        return self::init($st);
    }

    public static function generateToken(){
        $alphabet = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!#&/_-?";
        $token_length = 40;
        $token = "";

        for($_ = 0; $_ < $token_length; $_++){
            //Migrer til random_int ved php7.
            $rnd = mt_rand(0, strlen($alphabet) - 1);
            $token .= $alphabet[$rnd];
        }
        return $token;
    }


    public function isValidToken($type){
        return $this->type == $type && (time() - $this->time_issued) < $this->duration;
    }

    public static function createToken($type, $duration){

        $token = self::generateToken();

        $instance = new Token();
        $instance->setToken($token);
        $instance->setType($type);
        $instance->setTimeIssued(time());
        $instance->setDuration($duration);
        $instance->insertIntoDB();

        return $instance;
    }

    public function insertIntoDB(){

        $st = DB::getDB()->prepare('INSERT INTO token (token, type, duration, time_issued) VALUES (:token, :type, :duration, :time_issued)');
        $st->bindParam(':token', $this->token);
        $st->bindParam(':type', $this->type);
        $st->bindParam(':duration', $this->duration);
        $st->bindParam(':time_issued', $this->time_issued);
        $st->execute();
    }

    public function setToken($token){
        $this->token = $token;
    }

    public function setType($type){
        $this->type = $type;
    }

    public function setDuration($duration){
        if(is_numeric($duration)) {
            $this->duration = $duration;
        }
    }

    public function setTimeIssued($time){
        if(is_numeric($time)){
            $this->time_issued = $time;
        }
        elseif(is_numeric(strtotime($time))){
            $this->time_issued = strtotime($time);
        }
    }

    public function getToken(){
        return $this->token;
    }

    public function getType(){
        return $this->type;
    }

    public function getDuration(){
        return $this->duration;
    }

    public function getTimeIssued(){
        return $this->time_issued;
    }

}