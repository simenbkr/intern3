<?php

namespace intern3;


class StorhybelFordeling
{
    private $storhybel_id;
    private $velger_id;
    private $gammel_rom_id;
    private $ny_rom_id;
    private $romstr;

    private static function init(\PDOStatement $st)
    {

        $rad = $st->fetch();
        if ($rad === null) {
            return null;
        }

        $instans = new self();

        $instans->velger_id = $rad['velger_id'];
        $instans->storhybel_id = $rad['storhybel_id'];
        $instans->gammel_rom_id = array($rad['gammel_rom_id']);
        $instans->ny_rom_id = $rad['ny_rom_id'];

        return $instans;
    }

    private static function init_by_row($rad)
    {

        $instans = new self();

        $instans->velger_id = $rad['velger_id'];
        $instans->storhybel_id = $rad['storhybel_id'];
        $instans->gammel_rom_id = array($rad['gammel_rom_id']);
        $instans->ny_rom_id = $rad['ny_rom_id'];

        return $instans;
    }


    public static function medVelgerIdStorhybelId($velger_id, $storhybel_id): StorhybelFordeling
    {
        $st = DB::getDB()->prepare('SELECT * FROM storhybel_fordeling WHERE (velger_id=:vid AND storhybel_id=:sid) ORDER BY storhybel_id DESC LIMIT 1');
        $st->bindParam(':sid', $storhybel_id);
        $st->bindParam(':vid', $velger_id);
        $st->execute();

        return self::init($st);
    }

    public static function medStorhybelId($storhybel_id): array
    {
        $st = DB::getDB()->prepare('SELECT * FROM storhybel_fordeling WHERE (storhybel_id=:sid)');
        $st->bindParam(':sid', $storhybel_id);
        $st->execute();

        $arr = array();

        while ($rad = $st->fetch()) {

            if(isset($arr[$rad['velger_id']])) {
                $arr[$rad['velger_id']]->gammel_rom_id[] = $rad['gammel_rom_id'];

            } else {
                $arr[$rad['velger_id']] = self::init_by_row($rad);
            }
        }

        return $arr;
    }

    public function getVelgerId(): int
    {
        return $this->velger_id;
    }

    public function getStorhybelId(): int
    {
        return $this->storhybel_id;
    }

    public function getGammeltRomId(): array
    {
        return $this->gammel_rom_id;
    }

    public function getGammeltRom(): array
    {
        $arr = array();
        foreach($this->gammel_rom_id as $id) {
            $arr[] = Rom::medId($id);
        }

        return $arr;
    }

    public function getGammleRomAsString(): string {

        if(!is_null($this->romstr)) {
            return $this->romstr;
        } else {
            foreach($this->getGammeltRom() as $rom) {
                $ret[] = $rom->getNavn();
            }

            $this->romstr = implode(', ', $ret);
            return $this->romstr;
        }
    }

    public function getNyttRomId() //?int
    {
        return $this->ny_rom_id;
    }

    public function getNyttRom(): Rom
    {
        return Rom::medId($this->ny_rom_id);
    }

    public static function leggTilRom(int $storhybel_id, int $velger_id, int $rom_id) {

        $st = DB::getDB()->prepare('INSERT INTO storhybel_fordeling (storhybel_id,velger_id,gammel_rom_id) VALUES(:sid,:vid,:grid)');
        $st->bindParam(':sid', $storhybel_id);
        $st->bindParam(':vid', $velger_id);
        $st->bindParam(':grid', $rom_id);
        $st->execute();
    }

}