<?php

namespace intern3;


class StorhybelFordeling
{
    private $storhybel_id;
    private $beboer_id;
    private $gammel_rom_id;
    private $ny_rom_id;

    private static function init(\PDOStatement $st) {

        $rad = $st->fetch();
        if($rad === null) {
            return null;
        }

        $instans = new self();

        $instans->beboer_id     = $rad['beboer_id'];
        $instans->storhybel_id  = $rad['storhybel_id'];
        $instans->gammel_rom_id = $rad['gammel_rom_id'];
        $instans->ny_rom_id     = $rad['ny_rom_id'];

        return $instans;
    }

    private static function init_by_row($rad) {

        $instans = new self();

        $instans->beboer_id     = $rad['beboer_id'];
        $instans->storhybel_id  = $rad['storhybel_id'];
        $instans->gammel_rom_id = $rad['gammel_rom_id'];
        $instans->ny_rom_id     = $rad['ny_rom_id'];

        return $instans;
    }


    public static function medBeboerIdStorhybelId($beboer_id, $storhybel_id) : StorhybelFordeling {
        $st = DB::getDB()->prepare('SELECT * FROM storhybel_fordeling WHERE (beboer_id=:bid AND storhybel_id=:sid) ORDER BY storhybel_id DESC LIMIT 1');
        $st->bindParam(':sid', $storhybel_id);
        $st->bindParam(':bid', $beboer_id);
        $st->execute();

        return self::init($st);
    }

    public static function medStorhybelId($storhybel_id) : array {
        $st = DB::getDB()->prepare('SELECT * FROM storhybel_fordeling WHERE (storhybel_id=:sid)');
        $st->bindParam(':sid', $storhybel_id);
        $st->execute();

        $arr = array();

        while($rad = $st->fetch()) {
            $arr[$rad['beboer_id']] = self::init_by_row($rad);
        }

        return $arr;
    }

    public function getBeboerId() : int {
        return $this->beboer_id;
    }

    public function getStorhybelId() : int {
        return $this->storhybel_id;
    }

    public function getGammeltRomId() : int {
        return $this->gammel_rom_id;
    }

    public function getGammeltRom() : Rom {
        return Rom::medId($this->gammel_rom_id);
    }

    public function getNyttRomId() {
        return $this->ny_rom_id;
    }

    public function getNyttRom() : Rom {
        return Rom::medId($this->ny_rom_id);
    }


}