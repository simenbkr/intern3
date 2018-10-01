<?php

namespace intern3;


class Storhybelliste
{
    private $id;
    private $semester;
    private $ledige_rom;
    private $rekkefolge;
    private $neste;

    private static function init(\PDOStatement $st) : Storhybelliste {

        $rad = $st->fetch();
        if($rad === null){
            return null;
        }

        $instans = new self();

        $instans->id = $rad['id'];
        $instans->semester = $rad['semester'];
        $instans->ledige_rom = RomListe::alleLedige();

        return $instans;
    }

    public static function latest() : Storhybelliste {
        $st = DB::getDB()->prepare('SELECT * FROM storhybel ORDER BY id DESC LIMIT 1');
        return self::init($st);
    }

    public function getId() : int {
        return $this->id;
    }

    public function getSemester() : string {
        return $this->semester;
    }

    public function getLedigeRom() : array {
        return $this->ledige_rom;
    }

    public function getNeste() : Beboer {
        return $this->neste;
    }

    public function setLedigeRom(array $ledige_rom){
        $this->ledige_rom = $ledige_rom;
    }

    public function setNeste(Beboer $beboer){
        $this->neste = $beboer;
    }



}