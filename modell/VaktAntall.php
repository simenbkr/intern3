<?php

namespace intern3;


class VaktAntall
{

    private $brukerid;
    private $semester;
    private $antall;

    private static function init($st){

        $rad = $st->fetch();
        if ($rad == null) {
            return null;
        }
        $instance = new self();
        $instance->brukerid = $rad['bruker_id'];
        $instance->semester = $rad['semester'];
        $instance->antall = $rad['antall'];
        return $instance;
    }

    public function getBrukerId(){
        return $this->brukerid;
    }

    public function getBruker(){
        return Bruker::medId($this->brukerid);
    }

    public function getSemester(){
        return $this->semester;
    }

    public function getAntall(){
        return $this->antall;
    }

    public function endreAntall($antall){
        if($antall >= 0){

            $st = DB::getDB()->prepare('UPDATE vaktantall SET antall=:antall WHERE (bruker_id=:id AND semester=:sem)');
            $st->bindParam(':antall', $antall);
            $st->bindParam(':id', $this->brukerid);
            $st->bindParam(':sem', $this->semester);

            $st->execute();
            return true;
        }
        return false;
    }

    public static function add($brukerid, $semester, $antall){

        $st = DB::getDB()->prepare('INSERT INTO vaktantall (bruker_id, semester, antall) 
        VALUES(:brukerid, :semester, :antall)');

        $st->bindParam(':brukerid', $brukerid);
        $st->bindParam(':semester', $semester);
        $st->bindParam(':antall', $antall);

        $st->execute();
    }

    public static function medIdSemester($brukerid, $semester){

        $st = DB::getDB()->prepare('SELECT * FROM vaktantall WHERE (bruker_id=:id AND semester=:semester)');
        $st->bindParam(':id', $brukerid);
        $st->bindParam(':semester', $semester);

        $st->execute();

        return self::init($st);
    }

}